<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Pgsql;

use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Exception;
use Zend\Db\Adapter\Profiler;

class Pgsql implements DriverInterface, Profiler\ProfilerAwareInterface
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
     * @var array
     */
    protected $options = [
        'buffer_results' => false
    ];

    /**
     * Constructor
     *
     * @param array|Connection|resource $connection
     * @param null|Statement $statementPrototype
     * @param null|Result $resultPrototype
     * @param array $options
     */
    public function __construct(
        $connection,
        Statement $statementPrototype = null,
        Result $resultPrototype = null,
        $options = null
    ) {
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
     * @param Connection $connection
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
     * @param Statement $statement
     * @return self Provides a fluent interface
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
     * @param Result $result
     * @return self Provides a fluent interface
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
    public function getDatabasePlatformName(
        string $nameFormat = self::NAME_FORMAT_CAMELCASE
    ): string
    {
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            return 'Postgresql';
        }
        return 'PostgreSQL';
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function checkEnvironment(): void
    {
        if (! extension_loaded('pgsql')) {
            throw new Exception\RuntimeException(
                'The PostgreSQL (pgsql) extension is required for this adapter but the extension is not loaded'
            );
        }
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * @param mixed $resource
     */
    public function createStatement($resource = null): StatementInterface
    {
        $statement = clone $this->statementPrototype;

        if (is_string($resource)) {
            $statement->setSql($sqlOrResource);
        }
        if (! $this->connection->isConnected()) {
            $this->connection->connect();
        }

        $statement->initialize($this->connection->getResource());
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

    public function getPrepareType(): string
    {
        return self::PARAMETERIZATION_POSITIONAL;
    }

    /**
     * @param mixed  $type
     */
    public function formatParameterName(string $name, $type = null): string
    {
        return '$#';
    }

    public function getLastGeneratedValue(): string
    {
        return $this->connection->getLastGeneratedValue();
    }
}
