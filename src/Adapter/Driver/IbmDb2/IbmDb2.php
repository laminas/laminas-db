<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\IbmDb2;

use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Exception;
use Zend\Db\Adapter\Profiler;

class IbmDb2 implements DriverInterface, Profiler\ProfilerAwareInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /** @var Statement */
    protected $statementPrototype;

    /** @var Result */
    protected $resultPrototype;

    /**
     * @var Profiler\ProfilerInterface
     */
    protected $profiler;

    /**
     * @param array|Connection|resource $connection
     * @param null|Statement            $statementPrototype
     * @param null|Result               $resultPrototype
     */
    public function __construct($connection, Statement $statementPrototype = null, Result $resultPrototype = null)
    {
        if (! $connection instanceof Connection) {
            $connection = new Connection($connection);
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Result());
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
     * @param  Statement $statementPrototype
     * @return self Provides a fluent interface
     */
    public function registerStatementPrototype(Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
        return $this;
    }

    /**
     * @param  Result $resultPrototype
     * @return self Provides a fluent interface
     */
    public function registerResultPrototype(Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
        return $this;
    }

    public function getDatabasePlatformName(
        string $nameFormat = self::NAME_FORMAT_CAMELCASE
    ) : string
    {
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            return 'IbmDb2';
        } else {
            return 'IBM DB2';
        }
    }

    public function checkEnvironment(): void
    {
        if (! extension_loaded('ibm_db2')) {
            throw new Exception\RuntimeException('The ibm_db2 extension is required by this driver.');
        }
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * Create statement
     *
     * @param mixed $sqlOrResource
     */
    public function createStatement($resource = null): StatementInterface
    {
        $statement = clone $this->statementPrototype;
        if (is_resource($resource) && get_resource_type($resource) == 'DB2 Statement') {
            $statement->setResource($resource);
        } else {
            if (is_string($resource)) {
                $statement->setSql($resource);
            } elseif ($resource !== null) {
                throw new Exception\InvalidArgumentException(
                    __FUNCTION__ . ' only accepts an SQL string or an ibm_db2 resource'
                );
            }
            if (! $this->connection->isConnected()) {
                $this->connection->connect();
            }
            $statement->initialize($this->connection->getResource());
        }
        return $statement;
    }

    /**
     * {@inheritDoc}
     */
    public function createResult($resource): ResultInterface
    {
        $result = clone $this->resultPrototype;
        $result->initialize($resource, $this->connection->getLastGeneratedValue());
        return $result;
    }

    /**
     * Get prepare type
     *
     * @return string
     */
    public function getPrepareType(): string
    {
        return self::PARAMETERIZATION_POSITIONAL;
    }

    /**
     * Format parameter name
     *
     * @param mixed  $type
     */
    public function formatParameterName(string $name, $type = null): string
    {
        return '?';
    }

    public function getLastGeneratedValue(): string
    {
        return $this->connection->getLastGeneratedValue();
    }
}
