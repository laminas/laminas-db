<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Driver;

use Laminas\Db\Adapter\Profiler\ProfilerAwareInterface;
use Laminas\Db\Adapter\Profiler\ProfilerInterface;

abstract class AbstractConnection implements ConnectionInterface, ProfilerAwareInterface
{
    /**
     * @var array
     */
    protected $connectionParameters = [];

    /**
     * @var string|null
     */
    protected $driverName;

    /**
     * @var boolean
     */
    protected $inTransaction = false;

    /**
     * Nested transactions count.
     *
     * @var integer
     */
    protected $nestedTransactionsCount = 0;

    /**
     * @var ProfilerInterface|null
     */
    protected $profiler;

    /**
     * @var mixed
     */
    protected $resource;

    /**
     * {@inheritDoc}
     */
    public function disconnect(): ConnectionInterface
    {
        if ($this->isConnected()) {
            $this->resource = null;
        }

        return $this;
    }

    /**
     * Get connection parameters
     *
     * @return array
     */
    public function getConnectionParameters(): array
    {
        return $this->connectionParameters;
    }

    /**
     * Get driver name
     *
     * @return null|string
     */
    public function getDriverName(): ?string
    {
        return $this->driverName;
    }

    /**
     * @return null|ProfilerInterface
     */
    public function getProfiler(): ?ProfilerInterface
    {
        return $this->profiler;
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function getResource()
    {
        if (! $this->isConnected()) {
            $this->connect();
        }

        return $this->resource;
    }

    /**
     * Checks whether the connection is in transaction state.
     *
     * @return boolean
     */
    public function inTransaction(): bool
    {
        return $this->inTransaction;
    }

    /**
     * @param  array $connectionParameters
     * @return self Provides a fluent interface
     */
    public function setConnectionParameters(array $connectionParameters): ConnectionInterface
    {
        $this->connectionParameters = $connectionParameters;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return self Provides a fluent interface
     */
    public function setProfiler(ProfilerInterface $profiler): ConnectionInterface
    {
        $this->profiler = $profiler;

        return $this;
    }
}
