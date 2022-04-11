<?php

namespace Laminas\Db\Adapter\Driver\Sqlsrv;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Exception;
use Laminas\Db\Adapter\Profiler;

use function extension_loaded;
use function is_resource;
use function is_string;

class Sqlsrv implements DriverInterface, Profiler\ProfilerAwareInterface
{
    /** @var Connection */
    protected $connection;

    /** @var Statement */
    protected $statementPrototype;

    /** @var Result */
    protected $resultPrototype;

    /** @var null|Profiler\ProfilerInterface */
    protected $profiler;

    /**
     * @param array|Connection|resource $connection
     */
    public function __construct($connection, ?Statement $statementPrototype = null, ?Result $resultPrototype = null)
    {
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
    public function registerStatementPrototype(Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
        return $this;
    }

    /**
     * Register result prototype
     *
     * @return $this Provides a fluent interface
     */
    public function registerResultPrototype(Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
        return $this;
    }

    /**
     * Get database paltform name
     *
     * @param  string $nameFormat
     * @return string
     */
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        if ($nameFormat === self::NAME_FORMAT_CAMELCASE) {
            return 'SqlServer';
        }

        return 'SQLServer';
    }

    /**
     * Check environment
     *
     * @throws Exception\RuntimeException
     * @return void
     */
    public function checkEnvironment()
    {
        if (! extension_loaded('sqlsrv')) {
            throw new Exception\RuntimeException(
                'The Sqlsrv extension is required for this adapter but the extension is not loaded'
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
     * @param string|resource $sqlOrResource
     * @return Statement
     */
    public function createStatement($sqlOrResource = null)
    {
        $statement = clone $this->statementPrototype;
        if (is_resource($sqlOrResource)) {
            $statement->initialize($sqlOrResource);
        } else {
            if (! $this->connection->isConnected()) {
                $this->connection->connect();
            }
            $statement->initialize($this->connection->getResource());
            if (is_string($sqlOrResource)) {
                $statement->setSql($sqlOrResource);
            } elseif ($sqlOrResource !== null) {
                throw new Exception\InvalidArgumentException(
                    'createStatement() only accepts an SQL string or a Sqlsrv resource'
                );
            }
        }
        return $statement;
    }

    /**
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
     * @return string
     */
    public function getPrepareType()
    {
        return self::PARAMETERIZATION_POSITIONAL;
    }

    /**
     * @param string $name
     * @param mixed  $type
     * @return string
     */
    public function formatParameterName($name, $type = null)
    {
        return '?';
    }

    /**
     * @return mixed
     */
    public function getLastGeneratedValue()
    {
        return $this->getConnection()->getLastGeneratedValue();
    }
}
