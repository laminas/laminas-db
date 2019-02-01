<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Pdo;

use PDOStatement;
use resource;
use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Driver\Feature\AbstractFeature;
use Zend\Db\Adapter\Driver\Feature\DriverFeatureInterface;
use Zend\Db\Adapter\Exception;
use Zend\Db\Adapter\Profiler;

class Pdo implements DriverInterface, DriverFeatureInterface, Profiler\ProfilerAwareInterface
{
    /**
     * @const
     */
    const FEATURES_DEFAULT = 'default';

    /**
     * @var Connection
     */
    protected $connection = null;

    /**
     * @var Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Result
     */
    protected $resultPrototype = null;

    /**
     * @var array
     */
    protected $features = [];

    /**
     * @param array|Connection|\PDO $connection
     * @param null|Statement $statementPrototype
     * @param null|Result $resultPrototype
     * @param string $features
     */
    public function __construct(
        $connection,
        Statement $statementPrototype = null,
        Result $resultPrototype = null,
        $features = self::FEATURES_DEFAULT
    ) {
        if (! $connection instanceof Connection) {
            $connection = new Connection($connection);
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Result());
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
     * @param Profiler\ProfilerInterface $profiler
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
     * @param  Connection $connection
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
     *
     * @param Statement $statementPrototype
     */
    public function registerStatementPrototype(Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }

    /**
     * Register result prototype
     *
     * @param Result $resultPrototype
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
    public function addFeature(string $name, $feature): DriverFeatureInterface
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
    public function setupDefaultFeatures(): DriverFeatureInterface
    {
        $driverName = $this->connection->getDriverName();
        if ($driverName == 'sqlite') {
            $this->addFeature('', new Feature\SqliteRowCounter);
        } elseif ($driverName == 'oci') {
            $this->addFeature('', new Feature\OracleRowCounter);
        }
        return $this;
    }

    /**
     * Get feature
     *
     * @param $name
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
    public function getDatabasePlatformName(string $nameFormat = self::NAME_FORMAT_CAMELCASE): string
    {
        $name = $this->getConnection()->getDriverName();
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
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
     * @throws Exception\RuntimeException
     */
    public function checkEnvironment(): void
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
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * @param string|PDOStatement $resource
     */
    public function createStatement($resource = null): StatementInterface
    {
        $statement = clone $this->statementPrototype;
        if ($resource instanceof PDOStatement) {
            $statement->setResource($resource);
        } else {
            if (is_string($resource)) {
                $statement->setSql($resource);
            }
            if (! $this->connection->isConnected()) {
                $this->connection->connect();
            }
            $statement->initialize($this->connection->getResource());
        }
        return $statement;
    }

    /**
     * @param mixed $resource
     */
    public function createResult($resource): ResultInterface
    {
        $result = clone $this->resultPrototype;
        $result->initialize($resource, $this->connection->getLastGeneratedValue());

        return $result;
    }

    /**
     * @param mixed $resource
     */
    public function createResultWithSql($resource, string $sql): ResultInterface
    {
        $result = $this->createResult($resource);
        if (empty($this->connection)) {
            return $result;
        }
        if ($this->connection->getDriverName() == 'sqlite'
            && ($sqliteRowCounter = $this->getFeature('SqliteRowCounter'))
            && $resource->columnCount() > 0) {
            $result->setRowCount($sqliteRowCounter->getCountForSql($sql));
        }
        if ($this->connection->getDriverName() == 'oci'
            && ($oracleRowCounter = $this->getFeature('OracleRowCounter'))
            && $resource->columnCount() > 0) {
            $result->setRowCount($oracleRowCounter->getCountForSql($sql));
        }
        return $result;
    }

    /**
     * @param mixed $resource
     */
    public function createResultWithStatement($resource, StatementInterface $statement): ResultInterface
    {
        $result = $this->createResult($resource);
        if (empty($this->connection)) {
            return $result;
        }
        if ($this->connection->getDriverName() == 'sqlite'
            && ($sqliteRowCounter = $this->getFeature('SqliteRowCounter'))
            && $resource->columnCount() > 0) {
            $result->setRowCount($sqliteRowCounter->getCountForStatement($statement));
        }
        if ($this->connection->getDriverName() == 'oci'
            && ($oracleRowCounter = $this->getFeature('OracleRowCounter'))
            && $resource->columnCount() > 0) {
            $result->setRowCount($oracleRowCounter->getCountForStatement($statement));
        }
        return $result;
    }


    public function getPrepareType(): string
    {
        return self::PARAMETERIZATION_NAMED;
    }

    /**
     * @param string $name
     * @param string|null $type
     * @return string
     */
    public function formatParameterName(string $name, $type = null): string
    {
        if ($type === null && ! is_numeric($name) || $type == self::PARAMETERIZATION_NAMED) {
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

    public function getLastGeneratedValue(): string
    {
        return $this->connection->getLastGeneratedValue();
    }
}
