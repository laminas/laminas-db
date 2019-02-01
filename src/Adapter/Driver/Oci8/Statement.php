<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Oci8;

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
    protected $oci8 = null;

    /**
     * @var Oci8
     */
    protected $driver = null;

    /**
     * @var Profiler\ProfilerInterface
     */
    protected $profiler = null;

    /**
     * @var string
     */
    protected $sql = '';

    /**
     * Parameter container
     *
     * @var ParameterContainer
     */
    protected $parameterContainer = null;

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * Is prepared
     *
     * @var bool
     */
    protected $isPrepared = false;

    /**
     * @var bool
     */
    protected $bufferResults = false;

    /**
     * Set driver
     *
     * @param  Oci8 $driver
     * @return self Provides a fluent interface
     */
    public function setDriver($driver)
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

    public function initialize(resource $oci8)
    {
        $this->oci8 = $oci8;
        return $this;
    }

    public function setSql(string $sql): StatementContainerInterface
    {
        $this->sql = $sql;
        return $this;
    }

    public function setParameterContainer(ParameterContainer $parameterContainer): StatementContainerInterface
    {
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    public function getResource(): resource
    {
        return $this->resource;
    }

    public function setResource(resource $oci8Statement): self
    {
        $type = oci_statement_type($oci8Statement);
        if (false === $type || 'UNKNOWN' == $type) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid statement provided to %s',
                __METHOD__
            ));
        }
        $this->resource = $oci8Statement;
        $this->isPrepared = true;
        return $this;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function getParameterContainer(): ParameterContainer
    {
        return $this->parameterContainer;
    }

    public function isPrepared(): bool
    {
        return $this->isPrepared;
    }

    public function prepare(string $sql = null): StatementInterface
    {
        if ($this->isPrepared) {
            throw new Exception\RuntimeException('This statement has already been prepared');
        }

        $sql = ($sql) ?: $this->sql;

        // get oci8 statement resource
        $this->resource = oci_parse($this->oci8, $sql);

        if (! $this->resource) {
            $e = oci_error($this->oci8);
            throw new Exception\InvalidQueryException(
                'Statement couldn\'t be produced with sql: ' . $sql,
                null,
                new Exception\ErrorException($e['message'], $e['code'])
            );
        }

        $this->isPrepared = true;
        return $this;
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
            if ($parameters instanceof ParameterContainer) {
                $this->parameterContainer = $parameters;
            } else {
                $this->parameterContainer = new ParameterContainer($parameters);
            }
        }

        if ($this->parameterContainer->count() > 0) {
            $this->bindParametersFromContainer();
        }
        /** END Standard ParameterContainer Merging Block */

        if ($this->profiler) {
            $this->profiler->profilerStart($this);
        }

        if ($this->driver->getConnection()->inTransaction()) {
            $ret = @oci_execute($this->resource, OCI_NO_AUTO_COMMIT);
        } else {
            $ret = @oci_execute($this->resource, OCI_COMMIT_ON_SUCCESS);
        }

        if ($this->profiler) {
            $this->profiler->profilerFinish();
        }

        if ($ret === false) {
            $e = oci_error($this->resource);
            throw new Exception\RuntimeException($e['message'], $e['code']);
        }

        return $this->driver->createResultWithStatement($this->resource, $this);
    }

    /**
     * Bind parameters from container
     */
    protected function bindParametersFromContainer()
    {
        $parameters = $this->parameterContainer->getNamedArray();

        foreach ($parameters as $name => &$value) {
            if ($this->parameterContainer->offsetHasErrata($name)) {
                switch ($this->parameterContainer->offsetGetErrata($name)) {
                    case ParameterContainer::TYPE_NULL:
                        $type = null;
                        $value = null;
                        break;
                    case ParameterContainer::TYPE_DOUBLE:
                    case ParameterContainer::TYPE_INTEGER:
                        $type = SQLT_INT;
                        if (is_string($value)) {
                            $value = (int) $value;
                        }
                        break;
                    case ParameterContainer::TYPE_BINARY:
                        $type = SQLT_BIN;
                        break;
                    case ParameterContainer::TYPE_LOB:
                        $type = OCI_B_CLOB;
                        $clob = oci_new_descriptor($this->driver->getConnection()->getResource(), OCI_DTYPE_LOB);
                        $clob->writetemporary($value, OCI_TEMP_CLOB);
                        $value = $clob;
                        break;
                    case ParameterContainer::TYPE_STRING:
                    default:
                        $type = SQLT_CHR;
                        break;
                }
            } else {
                $type = SQLT_CHR;
            }

            $maxLength = -1;
            if ($this->parameterContainer->offsetHasMaxLength($name)) {
                $maxLength = $this->parameterContainer->offsetGetMaxLength($name);
            }

            oci_bind_by_name($this->resource, $name, $value, $maxLength, $type);
        }
    }

    /**
     * Perform a deep clone
     */
    public function __clone()
    {
        $this->isPrepared = false;
        $this->parametersBound = false;
        $this->resource = null;
        if ($this->parameterContainer) {
            $this->parameterContainer = clone $this->parameterContainer;
        }
    }
}
