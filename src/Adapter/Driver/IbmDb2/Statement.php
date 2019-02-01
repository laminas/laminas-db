<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\IbmDb2;

use ArrayAccess;
use ErrorException;
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
    protected $db2 = null;

    /**
     * @var IbmDb2
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
     * @var ParameterContainer
     */
    protected $parameterContainer = null;

    /**
     * @var bool
     */
    protected $isPrepared = false;

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * @param $resource
     * @return self Provides a fluent interface
     */
    public function initialize($resource)
    {
        $this->db2 = $resource;
        return $this;
    }

    /**
     * @param IbmDb2 $driver
     * @return self Provides a fluent interface
     */
    public function setDriver(IbmDb2 $driver)
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

    public function setSql(string $sql): StatementContainerInterface
    {
        $this->sql = $sql;
        return $this;
    }

    public function getSql(): string
    {
        return $this->sql;
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

    /**
     * @param $resource
     * @throws \Zend\Db\Adapter\Exception\InvalidArgumentException
     */
    public function setResource($resource)
    {
        if (get_resource_type($resource) !== 'DB2 Statement') {
            throw new Exception\InvalidArgumentException('Resource must be of type DB2 Statement');
        }
        $this->resource = $resource;
    }

    public function getResource(): resource
    {
        return $this->resource;
    }

    /**
     * Prepare sql
     *
     * @param string|null $sql
     * @return self Provides a fluent interface
     * @throws Exception\RuntimeException
     */
    public function prepare(string $sql = null): StatementInterface
    {
        if ($this->isPrepared) {
            throw new Exception\RuntimeException('This statement has been prepared already');
        }

        if ($sql === null) {
            $sql = $this->sql;
        }

        try {
            set_error_handler($this->createErrorHandler());
            $this->resource = db2_prepare($this->db2, $sql);
        } catch (ErrorException $e) {
            throw new Exception\RuntimeException($e->getMessage() . '. ' . db2_stmt_errormsg(), db2_stmt_error(), $e);
        } finally {
            restore_error_handler();
        }

        if ($this->resource === false) {
            throw new Exception\RuntimeException(db2_stmt_errormsg(), db2_stmt_error());
        }

        $this->isPrepared = true;
        return $this;
    }

    public function isPrepared(): bool
    {
        return $this->isPrepared;
    }

    public function execute(array $parameters = null): ResultInterface
    {
        if (! $this->isPrepared) {
            $this->prepare();
        }

        /** START Standard ParameterContainer Merging Block */
        if (! $this->parameterContainer instanceof ParameterContainer) {
            $this->parameterContainer = new ParameterContainer($parameters);
        }
        /** END Standard ParameterContainer Merging Block */

        if ($this->profiler) {
            $this->profiler->profilerStart($this);
        }

        set_error_handler(function () {
        }, E_WARNING); // suppress warnings
        $response = db2_execute($this->resource, $this->parameterContainer->getPositionalArray());
        restore_error_handler();

        if ($this->profiler) {
            $this->profiler->profilerFinish();
        }

        if ($response === false) {
            throw new Exception\RuntimeException(db2_stmt_errormsg($this->resource));
        }

        return $this->driver->createResult($this->resource);
    }

    /**
     * Creates and returns a callable error handler that raises exceptions.
     *
     * Only raises exceptions for errors that are within the error_reporting mask.
     *
     * @return callable
     */
    private function createErrorHandler()
    {
        /**
         * @param int $errno
         * @param string $errstr
         * @param string $errfile
         * @param int $errline
         * @return void
         * @throws ErrorException if error is not within the error_reporting mask.
         */
        return function ($errno, $errstr, $errfile, $errline) {
            if (! (error_reporting() & $errno)) {
                // error_reporting does not include this error
                return;
            }

            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        };
    }
}
