<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Oci8;

use resource;
use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Feature\AbstractFeature;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Exception;
use Zend\Db\Adapter\Profiler;

class Oci8 implements DriverInterface, Profiler\ProfilerAwareInterface
{
    const FEATURES_DEFAULT = 'default';

    /**
     * @var Connection
     */
    protected $connection = null;

    /**
     * @var Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Result
     */
    protected $resultPrototype = null;

    /**
     * @var Profiler\ProfilerInterface
     */
    protected $profiler = null;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $features = [];

    /**
     * @param array|Connection|\oci8 $connection
     * @param null|Statement $statementPrototype
     * @param null|Result $resultPrototype
     * @param array $options
     * @param string $features
     */
    public function __construct(
        $connection,
        Statement $statementPrototype = null,
        Result $resultPrototype = null,
        array $options = [],
        $features = self::FEATURES_DEFAULT
    ) {
        if (! $connection instanceof Connection) {
            $connection = new Connection($connection);
        }

        $options = array_intersect_key(array_merge($this->options, $options), $this->options);
        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Result());
        if (is_array($features)) {
            foreach ($features as $name => $feature) {
                $this->addFeature($name, $feature);
            }
        } elseif ($features instanceof AbstractFeature) {
            $this->addFeature($features->getName(), $features);
        } elseif ($features === self::FEATURES_DEFAULT) {
            $this->setupDefaultFeatures();
        }
    }

    /**
     * @param Profiler\ProfilerInterface $profiler
     * @return self Provides a fluent interface
     */
    public function setProfiler(Profiler\ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
        if ($this->connection instanceof Profiler\ProfilerAwareInterface) {
            $this->connection->setProfiler($profiler);
        }
        if ($this->statementPrototype instanceof Profiler\ProfilerAwareInterface) {
            $this->statementPrototype->setProfiler($profiler);
        }
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
     * Register connection
     *
     * @param  Connection $connection
     * @return self Provides a fluent interface
     */
    public function registerConnection(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this); // needs access to driver to createStatement()
        return $this;
    }

    /**
     * Register statement prototype
     *
     * @param Statement $statementPrototype
     * @return self Provides a fluent interface
     */
    public function registerStatementPrototype(Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this); // needs access to driver to createResult()
        return $this;
    }

    /**
     * @return null|Statement
     */
    public function getStatementPrototype()
    {
        return $this->statementPrototype;
    }

    /**
     * Register result prototype
     *
     * @param Result $resultPrototype
     * @return self Provides a fluent interface
     */
    public function registerResultPrototype(Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
        return $this;
    }

    /**
     * @return null|Result
     */
    public function getResultPrototype()
    {
        return $this->resultPrototype;
    }

    /**
     * Add feature
     *
     * @param string $name
     * @param AbstractFeature $feature
     * @return self Provides a fluent interface
     */
    public function addFeature($name, $feature)
    {
        if ($feature instanceof AbstractFeature) {
            $name = $feature->getName(); // overwrite the name, just in case
            $feature->setDriver($this);
        }
        $this->features[$name] = $feature;
        return $this;
    }

    /**
     * Setup the default features for Pdo
     *
     * @return self Provides a fluent interface
     */
    public function setupDefaultFeatures()
    {
        $this->addFeature(null, new Feature\RowCounter());
        return $this;
    }

    /**
     * Get feature
     *
     * @param string $name
     * @return AbstractFeature|false
     */
    public function getFeature($name)
    {
        if (isset($this->features[$name])) {
            return $this->features[$name];
        }
        return false;
    }

    /**
     * Get database platform name
     *
     * @param  string $nameFormat
     * @return string
     */
    public function getDatabasePlatformName(
        string $nameFormat = self::NAME_FORMAT_CAMELCASE
    ): string
    {
        return 'Oracle';
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function checkEnvironment(): void
    {
        if (! extension_loaded('oci8')) {
            throw new Exception\RuntimeException(
                'The Oci8 extension is required for this adapter but the extension is not loaded'
            );
        }
    }

    /**
     * @return Connection
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * @param mixed $resource
     */
    public function createStatement($resource = null): StatementInterface
    {
        $statement = clone $this->statementPrototype;
        if (is_resource($resource) && get_resource_type($resource) == 'oci8 statement') {
            $statement->setResource($resource);
        } else {
            if (is_string($resource)) {
                $statement->setSql($resource);
            } elseif ($resource !== null) {
                throw new Exception\InvalidArgumentException(
                    'Oci8 only accepts an SQL string or an oci8 resource in ' . __FUNCTION__
                );
            }
            if (! $this->connection->isConnected()) {
                $this->connection->connect();
            }
            $statement->initialize($this->connection->getResource());
        }
        return $statement;
    }

    /**
     * @param mixed $resource
     */
    public function createResult($resource): ResultInterface
    {
        $result = clone $this->resultPrototype;
        $result->initialize($resource, null);
        return $result;
    }

    public function createResultWithStatement(
        resource $resource,
        StatementInterface $statement
    ): ResultInterface
    {
        $result = $this->createResult($resource);
        if ($statement && ($rowCounter = $this->getFeature('RowCounter')) && oci_num_fields($resource) > 0) {
            $result->setRowCount($rowCounter->getRowCountClosure($context));
        }
        return $result;
    }

    public function getPrepareType(): string
    {
        return self::PARAMETERIZATION_NAMED;
    }

    /**
     * @param mixed  $type
     */
    public function formatParameterName(string $name, $type = null): string
    {
        return ':' . $name;
    }

    public function getLastGeneratedValue(): string
    {
        return $this->getConnection()->getLastGeneratedValue();
    }
}
