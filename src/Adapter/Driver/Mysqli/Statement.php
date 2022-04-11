<?php

namespace Laminas\Db\Adapter\Driver\Mysqli;

use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\Exception;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Profiler;
use mysqli_stmt;

use function array_unshift;
use function call_user_func_array;
use function is_array;

class Statement implements StatementInterface, Profiler\ProfilerAwareInterface
{
    /** @var \mysqli */
    protected $mysqli;

    /** @var Mysqli */
    protected $driver;

    /** @var Profiler\ProfilerInterface */
    protected $profiler;

    /** @var string */
    protected $sql = '';

    /**
     * Parameter container
     *
     * @var ParameterContainer
     */
    protected $parameterContainer;

    /** @var mysqli_stmt */
    protected $resource;

    /**
     * Is prepared
     *
     * @var bool
     */
    protected $isPrepared = false;

    /** @var bool */
    protected $bufferResults = false;

    /**
     * @param  bool $bufferResults
     */
    public function __construct($bufferResults = false)
    {
        $this->bufferResults = (bool) $bufferResults;
    }

    /**
     * Set driver
     *
     * @return $this Provides a fluent interface
     */
    public function setDriver(Mysqli $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return $this Provides a fluent interface
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
     * Initialize
     *
     * @return $this Provides a fluent interface
     */
    public function initialize(\mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        return $this;
    }

    /**
     * Set sql
     *
     * @param  string $sql
     * @return $this Provides a fluent interface
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * Set Parameter container
     *
     * @return $this Provides a fluent interface
     */
    public function setParameterContainer(ParameterContainer $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    /**
     * Get resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set resource
     *
     * @return $this Provides a fluent interface
     */
    public function setResource(mysqli_stmt $mysqliStatement)
    {
        $this->resource   = $mysqliStatement;
        $this->isPrepared = true;
        return $this;
    }

    /**
     * Get sql
     *
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Get parameter count
     *
     * @return ParameterContainer
     */
    public function getParameterContainer()
    {
        return $this->parameterContainer;
    }

    /**
     * Is prepared
     *
     * @return bool
     */
    public function isPrepared()
    {
        return $this->isPrepared;
    }

    /**
     * Prepare
     *
     * @param string $sql
     * @return $this Provides a fluent interface
     * @throws Exception\InvalidQueryException
     * @throws Exception\RuntimeException
     */
    public function prepare($sql = null)
    {
        if ($this->isPrepared) {
            throw new Exception\RuntimeException('This statement has already been prepared');
        }

        $sql = $sql ?: $this->sql;

        $this->resource = $this->mysqli->prepare($sql);
        if (! $this->resource instanceof mysqli_stmt) {
            throw new Exception\InvalidQueryException(
                'Statement couldn\'t be produced with sql: ' . $sql,
                $this->mysqli->errno,
                new Exception\ErrorException($this->mysqli->error, $this->mysqli->errno)
            );
        }

        $this->isPrepared = true;
        return $this;
    }

    /**
     * Execute
     *
     * @param null|array|ParameterContainer $parameters
     * @throws Exception\RuntimeException
     * @return mixed
     */
    public function execute($parameters = null)
    {
        if (! $this->isPrepared) {
            $this->prepare();
        }

        /** START Standard ParameterContainer Merging Block */
        if (! $this->parameterContainer instanceof ParameterContainer) {
            if ($parameters instanceof ParameterContainer) {
                $this->parameterContainer = $parameters;
                $parameters               = null;
            } else {
                $this->parameterContainer = new ParameterContainer();
            }
        }

        if (is_array($parameters)) {
            $this->parameterContainer->setFromArray($parameters);
        }

        if ($this->parameterContainer->count() > 0) {
            $this->bindParametersFromContainer();
        }
        /** END Standard ParameterContainer Merging Block */

        if ($this->profiler) {
            $this->profiler->profilerStart($this);
        }

        $return = $this->resource->execute();

        if ($this->profiler) {
            $this->profiler->profilerFinish();
        }

        if ($return === false) {
            throw new Exception\RuntimeException($this->resource->error);
        }

        if ($this->bufferResults === true) {
            $this->resource->store_result();
            $this->isPrepared = false;
            $buffered         = true;
        } else {
            $buffered = false;
        }

        return $this->driver->createResult($this->resource, $buffered);
    }

    /**
     * Bind parameters from container
     *
     * @return void
     */
    protected function bindParametersFromContainer()
    {
        $parameters = $this->parameterContainer->getNamedArray();
        $type       = '';
        $args       = [];

        foreach ($parameters as $name => &$value) {
            if ($this->parameterContainer->offsetHasErrata($name)) {
                switch ($this->parameterContainer->offsetGetErrata($name)) {
                    case ParameterContainer::TYPE_DOUBLE:
                        $type .= 'd';
                        break;
                    case ParameterContainer::TYPE_NULL:
                        $value = null; // as per @see http://www.php.net/manual/en/mysqli-stmt.bind-param.php#96148
                    case ParameterContainer::TYPE_INTEGER:
                        $type .= 'i';
                        break;
                    case ParameterContainer::TYPE_STRING:
                    default:
                        $type .= 's';
                        break;
                }
            } else {
                $type .= 's';
            }
            $args[] = &$value;
        }

        if ($args) {
            array_unshift($args, $type);
            call_user_func_array([$this->resource, 'bind_param'], $args);
        }
    }
}
