<?php

namespace Laminas\Db\Adapter\Driver\Pgsql;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Exception;
use Laminas\Db\Adapter\Profiler;

use function extension_loaded;
use function is_string;

class Pgsql implements DriverInterface, Profiler\ProfilerAwareInterface
{
    /** @var Connection */
    protected $connection;

    /** @var Statement */
    protected $statementPrototype;

    /** @var Result */
    protected $resultPrototype;

    /** @var null|Profiler\ProfilerInterface */
    protected $profiler;

    /** @var array */
    protected $options = [
        'buffer_results' => false,
    ];

    /**
     * Constructor
     *
     * @param array|Connection|resource $connection
     * @param array $options
     */
    public function __construct(
        $connection,
        ?Statement $statementPrototype = null,
        ?Result $resultPrototype = null,
        $options = null
    ) {
        if (! $connection instanceof Connection) {
            $connection = new Connection($connection);
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype($statementPrototype ?: new Statement());
        $this->registerResultPrototype($resultPrototype ?: new Result());
    }

    /**
     * @return $this Provides a fluent interface
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
     * @return $this Provides a fluent interface
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
     * @return $this Provides a fluent interface
     */
    public function registerStatementPrototype(Statement $statement)
    {
        $this->statementPrototype = $statement;
        $this->statementPrototype->setDriver($this); // needs access to driver to createResult()
        return $this;
    }

    /**
     * Register result prototype
     *
     * @return $this Provides a fluent interface
     */
    public function registerResultPrototype(Result $result)
    {
        $this->resultPrototype = $result;
        return $this;
    }

    /**
     * Get database platform name
     *
     * @param string $nameFormat
     * @return string
     */
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        if ($nameFormat === self::NAME_FORMAT_CAMELCASE) {
            return 'Postgresql';
        }

        return 'PostgreSQL';
    }

    /**
     * Check environment
     *
     * @throws Exception\RuntimeException
     * @return void
     */
    public function checkEnvironment()
    {
        if (! extension_loaded('pgsql')) {
            throw new Exception\RuntimeException(
                'The PostgreSQL (pgsql) extension is required for this adapter but the extension is not loaded'
            );
        }
    }

    /**
     * Get connection
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Create statement
     *
     * @param string|null $sqlOrResource
     * @return Statement
     */
    public function createStatement($sqlOrResource = null)
    {
        $statement = clone $this->statementPrototype;

        if (is_string($sqlOrResource)) {
            $statement->setSql($sqlOrResource);
        }

        if (! $this->connection->isConnected()) {
            $this->connection->connect();
        }

        $statement->initialize($this->connection->getResource());
        return $statement;
    }

    /**
     * Create result
     *
     * @param resource $resource
     * @return Result
     */
    public function createResult($resource)
    {
        $result = clone $this->resultPrototype;
        $result->initialize($resource, $this->connection->getLastGeneratedValue());
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
     * Get prepare Type
     *
     * @return string
     */
    public function getPrepareType()
    {
        return self::PARAMETERIZATION_POSITIONAL;
    }

    /**
     * Format parameter name
     *
     * @param string $name
     * @param mixed  $type
     * @return string
     */
    public function formatParameterName($name, $type = null)
    {
        return '$#';
    }

    /**
     * Get last generated value
     *
     * @param string $name
     * @return mixed
     */
    public function getLastGeneratedValue($name = null)
    {
        return $this->connection->getLastGeneratedValue($name);
    }
}
