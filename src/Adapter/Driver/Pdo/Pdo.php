<?php

namespace Laminas\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Feature\AbstractFeature;
use Laminas\Db\Adapter\Driver\Feature\DriverFeatureInterface;
use Laminas\Db\Adapter\Exception;
use Laminas\Db\Adapter\Profiler;
use PDOStatement;

use function extension_loaded;
use function is_array;
use function is_numeric;
use function is_string;
use function ltrim;
use function preg_match;
use function sprintf;
use function ucfirst;

class Pdo implements DriverInterface, DriverFeatureInterface, Profiler\ProfilerAwareInterface
{
    /**
     * @const
     */
    public const FEATURES_DEFAULT = 'default';

    /** @var Connection */
    protected $connection;

    /** @var Statement */
    protected $statementPrototype;

    /** @var Result */
    protected $resultPrototype;

    /** @var array */
    protected $features = [];

    /**
     * @param array|Connection|\PDO $connection
     * @param string $features
     */
    public function __construct(
        $connection,
        ?Statement $statementPrototype = null,
        ?Result $resultPrototype = null,
        $features = self::FEATURES_DEFAULT
    ) {
        if (! $connection instanceof Connection) {
            $connection = new Connection($connection);
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype($statementPrototype ?: new Statement());
        $this->registerResultPrototype($resultPrototype ?: new Result());
        if (is_array($features)) {
            foreach ($features as $name => $feature) {
                $this->addFeature($name, $feature);
            }
        } elseif ($features instanceof AbstractFeature) {
            $this->addFeature($features->getName(), $features);
        } elseif ($features === self::FEATURES_DEFAULT) {
            $this->setupDefaultFeatures();
        }
    }

    /**
     * @return self Provides a fluent interface
     */
    public function setProfiler(Profiler\ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
        if ($this->connection instanceof Profiler\ProfilerAwareInterface) {
            $this->connection->setProfiler($profiler);
        }
        if ($this->statementPrototype instanceof Profiler\ProfilerAwareInterface) {
            $this->statementPrototype->setProfiler($profiler);
        }
        return $this;
    }

    /**
     * @return null|Profiler\ProfilerInterface
     */
    public function getProfiler()
    {
        return $this->profiler;
    }

    /**
     * Register connection
     *
     * @return self Provides a fluent interface
     */
    public function registerConnection(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this);
        return $this;
    }

    /**
     * Register statement prototype
     */
    public function registerStatementPrototype(Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }

    /**
     * Register result prototype
     */
    public function registerResultPrototype(Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
    }

    /**
     * Add feature
     *
     * @param string $name
     * @param AbstractFeature $feature
     * @return self Provides a fluent interface
     */
    public function addFeature($name, $feature)
    {
        if ($feature instanceof AbstractFeature) {
            $name = $feature->getName(); // overwrite the name, just in case
            $feature->setDriver($this);
        }
        $this->features[$name] = $feature;
        return $this;
    }

    /**
     * Setup the default features for Pdo
     *
     * @return self Provides a fluent interface
     */
    public function setupDefaultFeatures()
    {
        $driverName = $this->connection->getDriverName();
        if ($driverName === 'sqlite') {
            $this->addFeature(null, new Feature\SqliteRowCounter());
            return $this;
        }

        if ($driverName === 'oci') {
            $this->addFeature(null, new Feature\OracleRowCounter());
            return $this;
        }

        return $this;
    }

    /**
     * Get feature
     *
     * @param string $name
     * @return AbstractFeature|false
     */
    public function getFeature($name)
    {
        if (isset($this->features[$name])) {
            return $this->features[$name];
        }
        return false;
    }

    /**
     * Get database platform name
     *
     * @param  string $nameFormat
     * @return string
     */
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        $name = $this->getConnection()->getDriverName();
        if ($nameFormat === self::NAME_FORMAT_CAMELCASE) {
            switch ($name) {
                case 'pgsql':
                    return 'Postgresql';
                case 'oci':
                    return 'Oracle';
                case 'dblib':
                case 'sqlsrv':
                    return 'SqlServer';
                default:
                    return ucfirst($name);
            }
        } else {
            switch ($name) {
                case 'sqlite':
                    return 'SQLite';
                case 'mysql':
                    return 'MySQL';
                case 'pgsql':
                    return 'PostgreSQL';
                case 'oci':
                    return 'Oracle';
                case 'dblib':
                case 'sqlsrv':
                    return 'SQLServer';
                default:
                    return ucfirst($name);
            }
        }
    }

    /**
     * Check environment
     */
    public function checkEnvironment()
    {
        if (! extension_loaded('PDO')) {
            throw new Exception\RuntimeException(
                'The PDO extension is required for this adapter but the extension is not loaded'
            );
        }
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string|PDOStatement $sqlOrResource
     * @return Statement
     */
    public function createStatement($sqlOrResource = null)
    {
        $statement = clone $this->statementPrototype;
        if ($sqlOrResource instanceof PDOStatement) {
            $statement->setResource($sqlOrResource);
        } else {
            if (is_string($sqlOrResource)) {
                $statement->setSql($sqlOrResource);
            }
            if (! $this->connection->isConnected()) {
                $this->connection->connect();
            }
            $statement->initialize($this->connection->getResource());
        }
        return $statement;
    }

    /**
     * @param resource $resource
     * @param mixed $context
     * @return Result
     */
    public function createResult($resource, $context = null)
    {
        $result   = clone $this->resultPrototype;
        $rowCount = null;

        // special feature, sqlite PDO counter
        if (
            $this->connection->getDriverName() === 'sqlite'
            && ($sqliteRowCounter = $this->getFeature('SqliteRowCounter'))
            && $resource->columnCount() > 0
        ) {
            $rowCount = $sqliteRowCounter->getRowCountClosure($context);
        }

        // special feature, oracle PDO counter
        if (
            $this->connection->getDriverName() === 'oci'
            && ($oracleRowCounter = $this->getFeature('OracleRowCounter'))
            && $resource->columnCount() > 0
        ) {
            $rowCount = $oracleRowCounter->getRowCountClosure($context);
        }

        $result->initialize($resource, $this->connection->getLastGeneratedValue(), $rowCount);
        return $result;
    }

    /**
     * @return Result
     */
    public function getResultPrototype()
    {
        return $this->resultPrototype;
    }

    /**
     * @return string
     */
    public function getPrepareType()
    {
        return self::PARAMETERIZATION_NAMED;
    }

    /**
     * @param string $name
     * @param string|null $type
     * @return string
     */
    public function formatParameterName($name, $type = null)
    {
        if ($type === null && ! is_numeric($name) || $type === self::PARAMETERIZATION_NAMED) {
            $name = ltrim($name, ':');
            // @see https://bugs.php.net/bug.php?id=43130
            if (preg_match('/[^a-zA-Z0-9_]/', $name)) {
                throw new Exception\RuntimeException(sprintf(
                    'The PDO param %s contains invalid characters.'
                    . ' Only alphabetic characters, digits, and underscores (_)'
                    . ' are allowed.',
                    $name
                ));
            }
            return ':' . $name;
        }

        return '?';
    }

    /**
     * @param string|null $name
     * @return string|null|false
     */
    public function getLastGeneratedValue($name = null)
    {
        return $this->connection->getLastGeneratedValue($name);
    }
}
