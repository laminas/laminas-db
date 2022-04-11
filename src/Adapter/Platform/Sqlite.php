<?php

namespace Laminas\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Pdo;
use Laminas\Db\Adapter\Exception;

class Sqlite extends AbstractPlatform
{
    /** @var string[] */
    protected $quoteIdentifier = ['"', '"'];

    /**
     * {@inheritDoc}
     */
    protected $quoteIdentifierTo = '\'';

    /** @var \PDO */
    protected $resource;

    /** @param null|Pdo\Pdo|\PDO $driver */
    public function __construct($driver = null)
    {
        if ($driver) {
            $this->setDriver($driver);
        }
    }

    /**
     * @param Pdo\Pdo|\PDO $driver
     * @return $this Provides a fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function setDriver($driver)
    {
        if (
            (
                $driver instanceof \PDO
                && $driver->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite'
            )
            || (
                $driver instanceof Pdo\Pdo
                && $driver->getDatabasePlatformName() === 'Sqlite'
            )
        ) {
            $this->resource = $driver;
            return $this;
        }

        throw new Exception\InvalidArgumentException(
            '$driver must be a Sqlite PDO Laminas\Db\Adapter\Driver, Sqlite PDO instance'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'SQLite';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue($value)
    {
        $resource = $this->resource;

        if ($resource instanceof DriverInterface) {
            $resource = $resource->getConnection()->getResource();
        }

        if ($resource instanceof \PDO) {
            return $resource->quote($value);
        }

        return parent::quoteValue($value);
    }

    /**
     * {@inheritDoc}
     */
    public function quoteTrustedValue($value)
    {
        $resource = $this->resource;

        if ($resource instanceof DriverInterface) {
            $resource = $resource->getConnection()->getResource();
        }

        if ($resource instanceof \PDO) {
            return $resource->quote($value);
        }

        return parent::quoteTrustedValue($value);
    }
}
