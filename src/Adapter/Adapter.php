<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Adapter\Profiler\ProfilerInterface;
use Laminas\Db\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;

use function func_get_args;
use function in_array;
use function is_array;
use function is_bool;
use function is_string;
use function strpos;
use function strtolower;

class Adapter implements AdapterInterface, Profiler\ProfilerAwareInterface
{
    /**
     * Query Mode Constants
     */
    const QUERY_MODE_EXECUTE = 'execute';
    const QUERY_MODE_PREPARE = 'prepare';

    /**
     * Prepare Type Constants
     */
    const PREPARE_TYPE_POSITIONAL = 'positional';
    const PREPARE_TYPE_NAMED = 'named';

    const FUNCTION_FORMAT_PARAMETER_NAME = 'formatParameterName';
    const FUNCTION_QUOTE_IDENTIFIER = 'quoteIdentifier';
    const FUNCTION_QUOTE_VALUE = 'quoteValue';

    const VALUE_QUOTE_SEPARATOR = 'quoteSeparator';

    /**
     * @var Driver\DriverInterface
     */
    protected $driver;

    /**
     * @var Platform\PlatformInterface
     */
    protected $platform = null;

    /**
     * @var Profiler\ProfilerInterface
     */
    protected $profiler = null;

    /**
     * @var ResultSet\ResultSetInterface
     */
    protected $queryResultSetPrototype = null;

    /**
     * @var Driver\StatementInterface
     */
    protected $lastPreparedStatement = null;

    public function __construct(
        DriverInterface $driver,
        PlatformInterface $platform = null,
        ResultSetInterface $queryResultPrototype = null,
        ProfilerInterface $profiler = null
    ) {
        $driver->checkEnvironment();
        $this->driver = $driver;
        $this->platform = $platform ?? self::createPlatform([], $driver);

        $this->queryResultSetPrototype = ($queryResultPrototype) ?: new ResultSet\ResultSet();

        if ($profiler) {
            $this->setProfiler($profiler);
        }
    }

    /**
     * @param Profiler\ProfilerInterface $profiler
     * @return self Provides a fluent interface
     */
    public function setProfiler(Profiler\ProfilerInterface $profiler): Adapter
    {
        $this->profiler = $profiler;
        if ($this->driver instanceof Profiler\ProfilerAwareInterface) {
            $this->driver->setProfiler($profiler);
        }
        return $this;
    }

    /**
     * @return null|Profiler\ProfilerInterface
     */
    public function getProfiler(): ?ProfilerInterface
    {
        return $this->profiler;
    }

    /**
     * getDriver()
     *
     * @throws Exception\RuntimeException
     */
    public function getDriver(): DriverInterface
    {
        if ($this->driver === null) {
            throw new Exception\RuntimeException('Driver has not been set or configured for this adapter.');
        }
        return $this->driver;
    }

    public function getPlatform(): ?PlatformInterface
    {
        return $this->platform;
    }

    public function getQueryResultSetPrototype(): ResultSetInterface
    {
        return $this->queryResultSetPrototype;
    }

    public function getCurrentSchema(): string
    {
        return $this->driver->getConnection()->getCurrentSchema();
    }

    /**
     * query() is a convenience function
     *
     * @param string|array|ParameterContainer $parametersOrQueryMode
     * @throws Exception\InvalidArgumentException
     * @return Driver\StatementInterface|ResultSet\ResultSet
     */
    public function query(
        string $sql,
        $parametersOrQueryMode = self::QUERY_MODE_PREPARE,
        ResultSetInterface $resultPrototype = null
    ) {
        if (is_string($parametersOrQueryMode)
            && in_array($parametersOrQueryMode, [self::QUERY_MODE_PREPARE, self::QUERY_MODE_EXECUTE])
        ) {
            $mode = $parametersOrQueryMode;
            $parameters = null;
        } elseif (is_array($parametersOrQueryMode) || $parametersOrQueryMode instanceof ParameterContainer) {
            $mode = self::QUERY_MODE_PREPARE;
            $parameters = $parametersOrQueryMode;
        } else {
            throw new Exception\InvalidArgumentException(
                'Parameter 2 to this method must be a flag, an array, or ParameterContainer'
            );
        }

        if ($mode == self::QUERY_MODE_PREPARE) {
            $this->lastPreparedStatement = null;
            $this->lastPreparedStatement = $this->driver->createStatement($sql);
            $this->lastPreparedStatement->prepare();
            if (is_array($parameters) || $parameters instanceof ParameterContainer) {
                if (is_array($parameters)) {
                    $this->lastPreparedStatement->setParameterContainer(new ParameterContainer($parameters));
                } else {
                    $this->lastPreparedStatement->setParameterContainer($parameters);
                }
                $result = $this->lastPreparedStatement->execute();
            } else {
                return $this->lastPreparedStatement;
            }
        } else {
            $result = $this->driver->getConnection()->execute($sql);
        }

        if ($result instanceof Driver\ResultInterface && $result->isQueryResult()) {
            $resultSet = clone ($resultPrototype ?: $this->queryResultSetPrototype);
            $resultSet->initialize($result);
            return $resultSet;
        }

        return $result;
    }

    public function createStatement(
        string $initialSql = null,
        ParameterContainer $initialParameters = null
    ): StatementInterface {
        $statement = $this->driver->createStatement($initialSql);
        if ($initialParameters === null
            || ! $initialParameters instanceof ParameterContainer
            && is_array($initialParameters)
        ) {
            $initialParameters = new ParameterContainer((is_array($initialParameters) ? $initialParameters : []));
        }
        $statement->setParameterContainer($initialParameters);
        return $statement;
    }

    public function getHelpers(): string
    {
        $functions = [];
        $platform = $this->platform;
        foreach (func_get_args() as $arg) {
            switch ($arg) {
                case self::FUNCTION_QUOTE_IDENTIFIER:
                    $functions[] = function ($value) use ($platform) {
                        return $platform->quoteIdentifier($value);
                    };
                    break;
                case self::FUNCTION_QUOTE_VALUE:
                    $functions[] = function ($value) use ($platform) {
                        return $platform->quoteValue($value);
                    };
                    break;
            }
        }
    }

    /**
     * @throws Exception\InvalidArgumentException
     * @return Driver\DriverInterface|Platform\PlatformInterface
     */
    public function __get(string $name)
    {
        switch (strtolower($name)) {
            case 'driver':
                return $this->driver;
            case 'platform':
                return $this->platform;
            default:
                throw new Exception\InvalidArgumentException('Invalid magic property on adapter');
        }
    }

    public static function factory(array $params): Adapter {
        $driver = self::createDriver($params);

        return new Adapter(
            $driver,
            self::createPlatform($params, $driver),
            null,
            isset($params['profiler']) ?  self::createProfiler($params) : null
        );
    }

    protected static function createDriver(array $parameters): DriverInterface
    {
        if (! isset($parameters['driver'])) {
            throw new Exception\InvalidArgumentException(
                __FUNCTION__ . ' expects a "driver" key to be present inside the parameters'
            );
        }

        if ($parameters['driver'] instanceof DriverInterface) {
            return $parameters['driver'];
        }

        if (! is_string($parameters['driver'])) {
            throw new Exception\InvalidArgumentException(
                __FUNCTION__ . ' expects a "driver" to be a string or instance of DriverInterface'
            );
        }

        $options = [];
        if (isset($parameters['options'])) {
            $options = (array) $parameters['options'];
            unset($parameters['options']);
        }

        $driverName = strtolower($parameters['driver']);
        switch ($driverName) {
            case 'mysqli':
                $driver = new Driver\Mysqli\Mysqli($parameters, null, null, $options);
                break;
            case 'sqlsrv':
                $driver = new Driver\Sqlsrv\Sqlsrv($parameters);
                break;
            case 'oci8':
                $driver = new Driver\Oci8\Oci8($parameters);
                break;
            case 'pgsql':
                $driver = new Driver\Pgsql\Pgsql($parameters);
                break;
            case 'ibmdb2':
                $driver = new Driver\IbmDb2\IbmDb2($parameters);
                break;
            case 'pdo':
            default:
                if ($driverName == 'pdo' || strpos($driverName, 'pdo') === 0) {
                    $driver = new Driver\Pdo\Pdo($parameters);
                }
        }

        if (! isset($driver) || ! $driver instanceof Driver\DriverInterface) {
            throw new Exception\InvalidArgumentException('DriverInterface expected', null, null);
        }

        return $driver;
    }

    protected static function createPlatform(
        array $parameters,
        DriverInterface $driver = null
    ): PlatformInterface
    {
        if (isset($parameters['platform'])) {
            $platformName = $parameters['platform'];
        } elseif ($driver instanceof DriverInterface) {
            $platformName = $driver->getDatabasePlatformName(DriverInterface::NAME_FORMAT_CAMELCASE);
        } else {
            throw new Exception\InvalidArgumentException(
                'A platform could not be determined from the provided configuration'
            );
        }

        // currently only supported by the IbmDb2 & Oracle concrete implementations
        $options = (isset($parameters['platform_options'])) ? $parameters['platform_options'] : [];

        switch ($platformName) {
            case 'Mysql':
                // mysqli or pdo_mysql driver
                if (!($driver instanceof Driver\Mysqli\Mysqli) && !($driver instanceof Driver\Pdo\Pdo)) {
                    $driver = null;
                }
                return new Platform\Mysql($driver);
            case 'SqlServer':
                // PDO is only supported driver for quoting values in this platform
                return new Platform\SqlServer(($driver instanceof Driver\Pdo\Pdo) ? $driver : null);
            case 'Oracle':
                if (!($driver instanceof Driver\Oci8\Oci8) && !($driver instanceof Driver\Pdo\Pdo)) {
                    $driver = null;
                }
                return new Platform\Oracle($options, $driver);
            case 'Sqlite':
                // PDO is only supported driver for quoting values in this platform
                if ($driver instanceof Driver\Pdo\Pdo) {
                    return new Platform\Sqlite($driver);
                }
                return new Platform\Sqlite(null);
            case 'Postgresql':
                // pgsql or pdo postgres driver
                if (!($driver instanceof Driver\Pgsql\Pgsql) && !($driver instanceof Driver\Pdo\Pdo)) {
                    $driver = null;
                }
                return new Platform\Postgresql($driver);
            case 'IbmDb2':
                // ibm_db2 driver escaping does not need an action connection
                return new Platform\IbmDb2($options);
            default:
                return new Platform\Sql92();
        }
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    protected function createProfiler(array $parameters): ProfilerInterface
    {
        if ($parameters['profiler'] instanceof Profiler\ProfilerInterface) {
            $profiler = $parameters['profiler'];
        } elseif (is_bool($parameters['profiler'])) {
            $profiler = ($parameters['profiler'] == true) ? new Profiler\Profiler : null;
        } else {
            throw new Exception\InvalidArgumentException(
                '"profiler" parameter must be an instance of ProfilerInterface or a boolean'
            );
        }
        return $profiler;
    }
}
