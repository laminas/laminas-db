<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use resource;
use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Exception;
use Zend\Db\Adapter\Profiler;

class Sqlsrv implements DriverInterface, Profiler\ProfilerAwareInterface
{
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
     * @var null|Profiler\ProfilerInterface
     */
    protected $profiler = null;

    /**
     * @param array|Connection|resource $connection
     * @param null|Statement $statementPrototype
     * @param null|Result $resultPrototype
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
     * @return self Provides a fluent interface
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
     * @param Result $resultPrototype
     * @return self Provides a fluent interface
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
    public function getDatabasePlatformName(
        string $nameFormat = self::NAME_FORMAT_CAMELCASE
    ) : string
    {
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            return 'SqlServer';
        }

        return 'SQLServer';
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function checkEnvironment(): void
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
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * @param string|resource $sqlOrResource
     */
    public function createStatement($resource = null): StatementInterface
    {
        $statement = clone $this->statementPrototype;
        if (is_resource($resource)) {
            $statement->initialize($resource);
        } else {
            if (! $this->connection->isConnected()) {
                $this->connection->connect();
            }
            $statement->initialize($this->connection->getResource());
            if (is_string($resource)) {
                $statement->setSql($resource);
            } elseif ($resource !== null) {
                throw new Exception\InvalidArgumentException(
                    'createStatement() only accepts an SQL string or a Sqlsrv resource'
                );
            }
        }
        return $statement;
    }

    public function createResult($resource): ResultInterface
    {
        $result = clone $this->resultPrototype;
        $result->initialize($resource, $this->connection->getLastGeneratedValue());
        return $result;
    }

    public function getPrepareType(): string
    {
        return self::PARAMETERIZATION_POSITIONAL;
    }

    /**
     * @param mixed  $type
     */
    public function formatParameterName(string $name, $type = null): string
    {
        return '?';
    }

    public function getLastGeneratedValue(): string
    {
        return $this->getConnection()->getLastGeneratedValue();
    }
}
