<?php

namespace Laminas\Db\Adapter\Driver\Mysqli;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Exception;
use Laminas\Db\Adapter\Profiler;
use mysqli_stmt;

use function array_intersect_key;
use function array_merge;
use function extension_loaded;
use function is_string;

class Mysqli implements DriverInterface, Profiler\ProfilerAwareInterface
{
    /** @var Connection */
    protected $connection;

    /** @var Statement */
    protected $statementPrototype;

    /** @var Result */
    protected $resultPrototype;

    /** @var Profiler\ProfilerInterface */
    protected $profiler;

    /** @var array */
    protected $options = [
        'buffer_results' => false,
    ];

    /**
     * Constructor
     *
     * @param array|Connection|\mysqli $connection
     * @param array $options
     */
    public function __construct(
        $connection,
        ?Statement $statementPrototype = null,
        ?Result $resultPrototype = null,
        array $options = []
    ) {
        if (! $connection instanceof Connection) {
            $connection = new Connection($connection);
        }

        $options = array_intersect_key(array_merge($this->options, $options), $this->options);

        $this->registerConnection($connection);
        $this->registerStatementPrototype($statementPrototype ?: new Statement($options['buffer_results']));
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
        $this->connection->setDriver($this); // needs access to driver to createStatement()
        return $this;
    }

    /**
     * Register statement prototype
     */
    public function registerStatementPrototype(Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this); // needs access to driver to createResult()
    }

    /**
     * Get statement prototype
     *
     * @return null|Statement
     */
    public function getStatementPrototype()
    {
        return $this->statementPrototype;
    }

    /**
     * Register result prototype
     */
    public function registerResultPrototype(Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
    }

    /**
     * @return null|Result
     */
    public function getResultPrototype()
    {
        return $this->resultPrototype;
    }

    /**
     * Get database platform name
     *
     * @param  string $nameFormat
     * @return string
     */
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        if ($nameFormat === self::NAME_FORMAT_CAMELCASE) {
            return 'Mysql';
        }

        return 'MySQL';
    }

    /**
     * Check environment
     *
     * @throws Exception\RuntimeException
     * @return void
     */
    public function checkEnvironment()
    {
        if (! extension_loaded('mysqli')) {
            throw new Exception\RuntimeException(
                'The Mysqli extension is required for this adapter but the extension is not loaded'
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
     * @param string $sqlOrResource
     * @return Statement
     */
    public function createStatement($sqlOrResource = null)
    {
        /**
         * @todo Resource tracking
        if (is_resource($sqlOrResource) && !in_array($sqlOrResource, $this->resources, true)) {
            $this->resources[] = $sqlOrResource;
        }
        */

        $statement = clone $this->statementPrototype;
        if ($sqlOrResource instanceof mysqli_stmt) {
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
     * Create result
     *
     * @param resource $resource
     * @param null|bool $isBuffered
     * @return Result
     */
    public function createResult($resource, $isBuffered = null)
    {
        $result = clone $this->resultPrototype;
        $result->initialize($resource, $this->connection->getLastGeneratedValue(), $isBuffered);
        return $result;
    }

    /**
     * Get prepare type
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
        return '?';
    }

    /**
     * Get last generated value
     *
     * @return mixed
     */
    public function getLastGeneratedValue()
    {
        return $this->getConnection()->getLastGeneratedValue();
    }
}
