<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Driver\Pgsql;

use Laminas\Db\Adapter\Driver\ConnectionInterface;
use Laminas\Db\Adapter\Exception;
use mysqli;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Adapter
 */
class Connection implements ConnectionInterface
{
    /**
     * @var Pgsql
     */
    protected $driver = null;

    /**
     * Connection parameters
     *
     * @var array
     */
    protected $connectionParameters = array();

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * In transaction
     *
     * @var boolean
     */
    protected $inTransaction = false;

    /**
     * Constructor
     *
     * @param mysqli|array|null $connectionInfo
     */
    public function __construct($connectionInfo = null)
    {
        if (is_array($connectionInfo)) {
            $this->setConnectionParameters($connectionInfo);
        } elseif ($connectionInfo instanceof mysqli) {
            $this->setResource($connectionInfo);
        }
    }

    /**
     * @param  array $connectionParameters
     * @return Connection
     */
    public function setConnectionParameters(array $connectionParameters)
    {
        $this->connectionParameters = $connectionParameters;
        return $this;
    }

    /**
     * @param  Pgsql $driver
     * @return Connection
     */
    public function setDriver(Pgsql $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @param  resource $resource
     * @return Connection
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return;
    }

    /**
     * @return null
     */
    public function getCurrentSchema()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $result = pg_query($this->resource, 'SELECT CURRENT_SCHEMA AS "currentschema"');
        if ($result == false) {
            return null;
        }
        return pg_fetch_result($result, 0, 'currentschema');
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Connect to the database
     *
     * @return void
     * @throws Exception\RuntimeException on failure
     */
    public function connect()
    {
        if (is_resource($this->resource)) {
            return;
        }

        // localize
        $p = $this->connectionParameters;

        // given a list of key names, test for existence in $p
        $findParameterValue = function(array $names) use ($p) {
            foreach ($names as $name) {
                if (isset($p[$name])) {
                    return $p[$name];
                }
            }
            return null;
        };

        $connection             = array();
        $connection['host']     = $findParameterValue(array('hostname', 'host'));
        $connection['user']     = $findParameterValue(array('username', 'user'));
        $connection['password'] = $findParameterValue(array('password', 'passwd', 'pw'));
        $connection['dbname']   = $findParameterValue(array('database', 'dbname', 'db', 'schema'));
        $connection['port']     = (isset($p['port'])) ? (int) $p['port'] : null;
        $connection['socket']   = (isset($p['socket'])) ? $p['socket'] : null;

        $connection = array_filter($connection); // remove nulls
        $connection = http_build_query($connection, null, ' '); // @link http://php.net/pg_connect

        $this->resource = pg_connect($connection);

        if ($this->resource === false) {
            throw new Exception\RuntimeException(sprintf(
                '%s: Unable to connect to database',
                __METHOD__
            ));
        }
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return (is_resource($this->resource));
    }

    /**
     * @return void
     */
    public function disconnect()
    {
        pg_close($this->resource);
    }

    /**
     * @return void
     */
    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    /**
     * @return void
     */
    public function commit()
    {
        // TODO: Implement commit() method.
    }

    /**
     * @return void
     */
    public function rollback()
    {
        // TODO: Implement rollback() method.
    }

    /**
     * @param  string $sql
     * @return resource|\Laminas\Db\ResultSet\ResultSetInterface
     */
    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $resultResource = pg_query($this->resource, $sql);

        //var_dump(pg_result_status($resultResource));

        // if the returnValue is something other than a mysqli_result, bypass wrapping it
        if ($resultResource === false) {
            throw new Exception\InvalidQueryException(pg_errormessage());
        }

        $resultPrototype = $this->driver->createResult(($resultResource === true) ? $this->resource : $resultResource);
        return $resultPrototype;
    }

    /**
     * @param  null $name Ignored
     * @return string
     */
    public function getLastGeneratedValue($name = null)
    {
        if ($name == null) {
            return null;
        }
        $result = pg_query($this->resource, 'SELECT CURRVAL(\'' . str_replace('\'', '\\\'', $name) . '\') as "currval"');
        return pg_fetch_result($result, 0, 'currval');
    }

}
