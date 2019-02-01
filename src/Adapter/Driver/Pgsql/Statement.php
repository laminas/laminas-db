<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Pgsql;

use ArrayAccess;
use resource;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Exception;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Profiler;
use Zend\Db\Adapter\StatementContainerInterface;

class Statement implements StatementInterface, Profiler\ProfilerAwareInterface
{
    /**
     * @var int
     */
    protected static $statementIndex = 0;

    /**
     * @var string
     */
    protected $statementName = '';

    /**
     * @var Pgsql
     */
    protected $driver = null;

    /**
     * @var Profiler\ProfilerInterface
     */
    protected $profiler = null;

    /**
     * @var resource
     */
    protected $pgsql = null;

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * @var string
     */
    protected $sql;

    /**
     * @var ParameterContainer
     */
    protected $parameterContainer;

    /**
     * @param  Pgsql $driver
     * @return self Provides a fluent interface
     */
    public function setDriver(Pgsql $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @param Profiler\ProfilerInterface $profiler
     * @return self Provides a fluent interface
     */
    public function setProfiler(Profiler\ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
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
     * @throws Exception\RuntimeException for invalid or missing postgresql connection
     */
    public function initialize(resource $pgsql): void
    {
        if (! is_resource($pgsql) || get_resource_type($pgsql) !== 'pgsql link') {
            throw new Exception\RuntimeException(sprintf(
                '%s: Invalid or missing postgresql connection; received "%s"',
                __METHOD__,
                get_resource_type($pgsql)
            ));
        }
        $this->pgsql = $pgsql;
    }

    /**
     * @throws Exception\ErrorException if the resource is empty
     */
    public function getResource(): resource
    {
        if (is_null($this->resource)) {
            throw new Exception\ErrorException('The resource is empty');
        }
        return $this->resource;
    }

    public function setSql(string $sql): StatementContainerInterface
    {
        $this->sql = $sql;
        return $this;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * Set parameter container
     *
     * @param ParameterContainer $parameterContainer
     * @return self Provides a fluent interface
     */
    public function setParameterContainer(ParameterContainer $parameterContainer): StatementContainerInterface
    {
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    /**
     * Get parameter container
     *
     * @return ParameterContainer
     */
    public function getParameterContainer(): ParameterContainer
    {
        return $this->parameterContainer;
    }

    /**
     * Prepare
     *
     * @param string $sql
     */
    public function prepare(string $sql = null): StatementInterface
    {
        $sql = ($sql) ?: $this->sql;

        $pCount = 1;
        $sql = preg_replace_callback(
            '#\$\##',
            function () use (&$pCount) {
                return '$' . $pCount++;
            },
            $sql
        );

        $this->sql = $sql;
        $this->statementName = 'statement' . ++static::$statementIndex;
        $this->resource = pg_prepare($this->pgsql, $this->statementName, $sql);

        return $this;
    }

    public function isPrepared(): bool
    {
        return isset($this->resource);
    }

    /**
     * @throws Exception\InvalidQueryException
     */
    public function execute(array $parameters = null): ResultInterface
    {
        if (! $this->isPrepared()) {
            $this->prepare();
        }

        /** START Standard ParameterContainer Merging Block */
        if (! $this->parameterContainer instanceof ParameterContainer) {
            $this->parameterContainer = new ParameterContainer($parameters);
        }

        if ($this->parameterContainer->count() > 0) {
            $parameters = $this->parameterContainer->getPositionalArray();
        }
        /** END Standard ParameterContainer Merging Block */

        if ($this->profiler) {
            $this->profiler->profilerStart($this);
        }

        $resultResource = pg_execute($this->pgsql, $this->statementName, (array) $parameters);

        if ($this->profiler) {
            $this->profiler->profilerFinish();
        }

        if ($resultResource === false) {
            throw new Exception\InvalidQueryException(pg_last_error());
        }

        return $this->driver->createResult($resultResource);
    }
}
