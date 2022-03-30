<?php

namespace Laminas\Db\Adapter\Driver\Mysqli;

use Exception as GenericException;
use Laminas\Db\Adapter\Driver\AbstractConnection;
use Laminas\Db\Adapter\Exception;
use Laminas\Db\Adapter\Exception\InvalidArgumentException;

use function constant;
use function defined;
use function is_array;
use function is_string;
use function strtoupper;

use const MYSQLI_CLIENT_SSL;
use const MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;

class Connection extends AbstractConnection
{
    /** @var Mysqli */
    protected $driver;

    /** @var \mysqli */
    protected $resource;

    /**
     * Constructor
     *
     * @param array|mysqli|null $connectionInfo
     * @throws InvalidArgumentException
     */
    public function __construct($connectionInfo = null)
    {
        if (is_array($connectionInfo)) {
            $this->setConnectionParameters($connectionInfo);
        } elseif ($connectionInfo instanceof \mysqli) {
            $this->setResource($connectionInfo);
        } elseif (null !== $connectionInfo) {
            throw new Exception\InvalidArgumentException(
                '$connection must be an array of parameters, a mysqli object or null'
            );
        }
    }

    /**
     * @return self Provides a fluent interface
     */
    public function setDriver(Mysqli $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentSchema()
    {
        if (! $this->isConnected()) {
            $this->connect();
        }

        $result = $this->resource->query('SELECT DATABASE()');
        $r      = $result->fetch_row();

        return $r[0];
    }

    /**
     * Set resource
     *
     * @return self Provides a fluent interface
     */
    public function setResource(\mysqli $resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function connect()
    {
        if ($this->resource instanceof \mysqli) {
            return $this;
        }

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

        $hostname = $findParameterValue(['hostname', 'host']);
        $username = $findParameterValue(['username', 'user']);
        $password = $findParameterValue(['password', 'passwd', 'pw']);
        $database = $findParameterValue(['database', 'dbname', 'db', 'schema']);
        $port     = isset($p['port']) ? (int) $p['port'] : null;
        $socket   = $p['socket'] ?? null;

        // phpcs:ignore WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps
        $useSSL     = $p['use_ssl'] ?? 0;
        $clientKey  = $p['client_key'] ?? '';
        $clientCert = $p['client_cert'] ?? '';
        $caCert     = $p['ca_cert'] ?? '';
        $caPath     = $p['ca_path'] ?? '';
        $cipher     = $p['cipher'] ?? '';

        $this->resource = $this->createResource();

        if (! empty($p['driver_options'])) {
            foreach ($p['driver_options'] as $option => $value) {
                if (is_string($option)) {
                    $option = strtoupper($option);
                    if (! defined($option)) {
                        continue;
                    }
                    $option = constant($option);
                }
                $this->resource->options($option, $value);
            }
        }

        $flags = null;

        // phpcs:ignore WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps
        if ($useSSL && ! $socket) {
            // Even though mysqli docs are not quite clear on this, MYSQLI_CLIENT_SSL
            // needs to be set to make sure SSL is used. ssl_set can also cause it to
            // be implicitly set, but only when any of the parameters is non-empty.
            $flags = MYSQLI_CLIENT_SSL;
            $this->resource->ssl_set($clientKey, $clientCert, $caCert, $caPath, $cipher);
            //MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT is not valid option, needs to be set as flag
            if (
                isset($p['driver_options'])
                && isset($p['driver_options'][MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT])
            ) {
                $flags |= MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
            }
        }

        try {
            $flags === null
                ? $this->resource->real_connect($hostname, $username, $password, $database, $port, $socket)
                : $this->resource->real_connect($hostname, $username, $password, $database, $port, $socket, $flags);
        } catch (GenericException $e) {
            throw new Exception\RuntimeException(
                'Connection error',
                $this->resource->connect_errno,
                new Exception\ErrorException($this->resource->connect_error, $this->resource->connect_errno)
            );
        }

        if ($this->resource->connect_error) {
            throw new Exception\RuntimeException(
                'Connection error',
                $this->resource->connect_errno,
                new Exception\ErrorException($this->resource->connect_error, $this->resource->connect_errno)
            );
        }

        if (! empty($p['charset'])) {
            $this->resource->set_charset($p['charset']);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isConnected()
    {
        return $this->resource instanceof \mysqli;
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect()
    {
        if ($this->resource instanceof \mysqli) {
            $this->resource->close();
        }
        $this->resource = null;
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
        if (! $this->isConnected()) {
            $this->connect();
        }

        $this->resource->autocommit(false);
        $this->inTransaction = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        if (! $this->isConnected()) {
            $this->connect();
        }

        $this->resource->commit();
        $this->inTransaction = false;
        $this->resource->autocommit(true);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function rollback()
    {
        if (! $this->isConnected()) {
            throw new Exception\RuntimeException('Must be connected before you can rollback.');
        }

        if (! $this->inTransaction) {
            throw new Exception\RuntimeException('Must call beginTransaction() before you can rollback.');
        }

        $this->resource->rollback();
        $this->resource->autocommit(true);
        $this->inTransaction = false;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\InvalidQueryException
     */
    public function execute($sql)
    {
        if (! $this->isConnected()) {
            $this->connect();
        }

        if ($this->profiler) {
            $this->profiler->profilerStart($sql);
        }

        $resultResource = $this->resource->query($sql);

        if ($this->profiler) {
            $this->profiler->profilerFinish($sql);
        }

        // if the returnValue is something other than a mysqli_result, bypass wrapping it
        if ($resultResource === false) {
            throw new Exception\InvalidQueryException($this->resource->error);
        }

        return $this->driver->createResult($resultResource === true ? $this->resource : $resultResource);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastGeneratedValue($name = null)
    {
        return $this->resource->insert_id;
    }

    /**
     * Create a new mysqli resource
     *
     * @return \mysqli
     */
    protected function createResource()
    {
        return new \mysqli();
    }
}
