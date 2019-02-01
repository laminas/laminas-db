<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Pgsql;

use Zend\Db\Adapter\Driver\AbstractConnection;
use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Exception;

class Connection extends AbstractConnection
{
    /**
     * @var Pgsql
     */
    protected $driver = null;

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * @var null|int PostgreSQL connection type
     */
    protected $type = null;

    /**
     * Constructor
     *
     * @param resource|array|null $connectionInfo
     */
    public function __construct($connectionInfo = null)
    {
        if (is_array($connectionInfo)) {
            $this->setConnectionParameters($connectionInfo);
        } elseif (is_resource($connectionInfo)) {
            $this->setResource($connectionInfo);
        }
    }

    /**
     * Set resource
     *
     * @param resource $resource
     * @return self Provides a fluent interface
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }


    /**
     * Set driver
     *
     * @param  Pgsql $driver
     * @return self Provides a fluent interface
     */
    public function setDriver(Pgsql $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @param int|null $type
     * @return self Provides a fluent interface
     */
    public function setType($type)
    {
        $invalidConectionType = ($type !== PGSQL_CONNECT_FORCE_NEW);

        // Compatibility with PHP < 5.6
        if ($invalidConectionType && defined('PGSQL_CONNECT_ASYNC')) {
            $invalidConectionType = ($type !== PGSQL_CONNECT_ASYNC);
        }

        if ($invalidConectionType) {
            throw new Exception\InvalidArgumentException(
                'Connection type is not valid. (See: http://php.net/manual/en/function.pg-connect.php)'
            );
        }
        $this->type = $type;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentSchema(): string
    {
        if (! $this->isConnected()) {
            $this->connect();
        }

        $result = pg_query($this->resource, 'SELECT CURRENT_SCHEMA AS "currentschema"');
        if ($result == false) {
            return '';
        }

        return pg_fetch_result($result, 0, 'currentschema');
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\RuntimeException on failure
     */
    public function connect(): ConnectionInterface
    {
        if (is_resource($this->resource)) {
            return $this;
        }

        $connection = $this->getConnectionString();
        set_error_handler(function ($number, $string) {
            throw new Exception\RuntimeException(
                __METHOD__ . ': Unable to connect to database',
                $number,
                new Exception\ErrorException($string, $number)
            );
        });
        $this->resource = pg_connect($connection);
        restore_error_handler();

        if ($this->resource === false) {
            throw new Exception\RuntimeException(sprintf(
                '%s: Unable to connect to database',
                __METHOD__
            ));
        }

        $p = $this->connectionParameters;

        if (! empty($p['charset'])) {
            if (-1 === pg_set_client_encoding($this->resource, $p['charset'])) {
                throw new Exception\RuntimeException(sprintf(
                    "%s: Unable to set client encoding '%s'",
                    __METHOD__,
                    $p['charset']
                ));
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isConnected(): bool
    {
        return (is_resource($this->resource));
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect(): ConnectionInterface
    {
        pg_close($this->resource);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction(): ConnectionInterface
    {
        if ($this->inTransaction()) {
            throw new Exception\RuntimeException('Nested transactions are not supported');
        }

        if (! $this->isConnected()) {
            $this->connect();
        }

        pg_query($this->resource, 'BEGIN');
        $this->inTransaction = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function commit(): ConnectionInterface
    {
        if (! $this->isConnected()) {
            $this->connect();
        }

        if (! $this->inTransaction()) {
            throw new Exception\RuntimeException('The transaction has not been started');
        }

        pg_query($this->resource, 'COMMIT');
        $this->inTransaction = false;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function rollback(): ConnectionInterface
    {
        if (! $this->isConnected()) {
            throw new Exception\RuntimeException('Must be connected before you can rollback');
        }

        if (! $this->inTransaction()) {
            throw new Exception\RuntimeException('Must call beginTransaction() before you can rollback');
        }

        pg_query($this->resource, 'ROLLBACK');
        $this->inTransaction = false;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\InvalidQueryException
     */
    public function execute(string $sql): ResultInterface
    {
        if (! $this->isConnected()) {
            $this->connect();
        }

        if ($this->profiler) {
            $this->profiler->profilerStart($sql);
        }

        $resultResource = pg_query($this->resource, $sql);

        if ($this->profiler) {
            $this->profiler->profilerFinish($sql);
        }

        // if the returnValue is something other than a pg result resource, bypass wrapping it
        if ($resultResource === false) {
            throw new Exception\InvalidQueryException(pg_errormessage());
        }

        $resultPrototype = $this->driver->createResult(($resultResource === true) ? $this->resource : $resultResource);

        return $resultPrototype;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastGeneratedValue($name = null): string
    {
        if ($name === null) {
            return '';
        }
        $result = pg_query(
            $this->resource,
            'SELECT CURRVAL(\'' . str_replace('\'', '\\\'', $name) . '\') as "currval"'
        );

        return pg_fetch_result($result, 0, 'currval');
    }

    /**
     * Get Connection String
     *
     * @return string
     */
    private function getConnectionString()
    {
        // localize
        $p = $this->connectionParameters;

        // given a list of key names, test for existence in $p
        $findParameterValue = function (array $names) use ($p) {
            foreach ($names as $name) {
                if (isset($p[$name])) {
                    return $p[$name];
                }
            }
            return;
        };

        $connectionParameters = [
            'host'     => $findParameterValue(['hostname', 'host']),
            'user'     => $findParameterValue(['username', 'user']),
            'password' => $findParameterValue(['password', 'passwd', 'pw']),
            'dbname'   => $findParameterValue(['database', 'dbname', 'db', 'schema']),
            'port'     => isset($p['port']) ? (int) $p['port'] : null,
            'socket'   => isset($p['socket']) ? $p['socket'] : null,
        ];

        return urldecode(http_build_query(array_filter($connectionParameters), '', ' '));
    }
}
