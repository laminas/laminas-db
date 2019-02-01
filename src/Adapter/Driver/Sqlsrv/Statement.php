<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Sqlsrv;

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
     * @var resource
     */
    protected $sqlsrv = null;

    /**
     * @var Sqlsrv
     */
    protected $driver = null;

    /**
     * @var Profiler\ProfilerInterface
     */
    protected $profiler = null;

    /**
     * @var string
     */
    protected $sql = null;

    /**
     * @var bool
     */
    protected $isQuery = null;

    /**
     * @var array
     */
    protected $parameterReferences = [];

    /**
     * @var ParameterContainer
     */
    protected $parameterContainer = null;

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     *
     * @var bool
     */
    protected $isPrepared = false;

    /**
     * @var array
     */
    protected $prepareParams = [];

    /**
     * @var array
     */
    protected $prepareOptions = [];

    /**
     * @var array
     */
    protected $parameterReferenceValues = [];

    /**
     * Set driver
     *
     * @param  Sqlsrv $driver
     * @return self Provides a fluent interface
     */
    public function setDriver(Sqlsrv $driver)
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
     *
     * One of two resource types will be provided here:
     * a) "SQL Server Connection" when a prepared statement needs to still be produced
     * b) "SQL Server Statement" when a prepared statement has been already produced
     * (there will need to already be a bound param set if it applies to this query)
     *
     * @param resource $resource
     * @return self Provides a fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function initialize($resource)
    {
        $resourceType = get_resource_type($resource);

        if ($resourceType == 'SQL Server Connection') {
            $this->sqlsrv = $resource;
        } elseif ($resourceType == 'SQL Server Statement') {
            $this->resource = $resource;
            $this->isPrepared = true;
        } else {
            throw new Exception\InvalidArgumentException('Invalid resource provided to ' . __CLASS__);
        }

        return $this;
    }

    public function setParameterContainer(ParameterContainer $parameterContainer): StatementContainerInterface
    {
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    public function getParameterContainer(): ParameterContainer
    {
        return $this->parameterContainer;
    }

    public function setResource(resource $resource)
    {
        $this->resource = $resource;
        return $this;
    }

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
     * @throws Exception\RuntimeException
     */
    public function prepare(string $sql = null): StatementInterface
    {
        if ($this->isPrepared) {
            throw new Exception\RuntimeException('Already prepared');
        }
        $sql = ($sql) ?: $this->sql;

        $pRef = &$this->parameterReferences;
        for ($position = 0, $count = substr_count($sql, '?'); $position < $count; $position++) {
            if (! isset($this->prepareParams[$position])) {
                $pRef[$position] = [&$this->parameterReferenceValues[$position], SQLSRV_PARAM_IN, null, null];
            } else {
                $pRef[$position] = &$this->prepareParams[$position];
            }
        }

        $this->resource = sqlsrv_prepare($this->sqlsrv, $sql, $pRef, $this->prepareOptions);

        $this->isPrepared = true;

        return $this;
    }

    public function isPrepared(): bool
    {
        return $this->isPrepared;
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function execute(array $parameters = null): ResultInterface
    {
        if (! $this->isPrepared) {
            $this->prepare();
        }

        /** START Standard ParameterContainer Merging Block */
        if (! $this->parameterContainer instanceof ParameterContainer) {
            $this->parameterContainer = new ParameterContainer($parameters);
        }

        if ($this->parameterContainer->count() > 0) {
            $this->bindParametersFromContainer();
        }
        /** END Standard ParameterContainer Merging Block */

        if ($this->profiler) {
            $this->profiler->profilerStart($this);
        }

        $resultValue = sqlsrv_execute($this->resource);

        if ($this->profiler) {
            $this->profiler->profilerFinish();
        }

        if ($resultValue === false) {
            $errors = sqlsrv_errors();
            // ignore general warnings
            if ($errors[0]['SQLSTATE'] != '01000') {
                throw new Exception\RuntimeException($errors[0]['message']);
            }
        }

        return $this->driver->createResult($this->resource);
    }

    /**
     * Bind parameters from container
     *
     */
    protected function bindParametersFromContainer()
    {
        $values = $this->parameterContainer->getPositionalArray();
        $position = 0;
        foreach ($values as $value) {
            $this->parameterReferences[$position++][0] = $value;
        }
    }

    public function setPrepareParams(array $prepareParams): void
    {
        $this->prepareParams = $prepareParams;
    }

    public function setPrepareOptions(array $prepareOptions): void
    {
        $this->prepareOptions = $prepareOptions;
    }
}
