<?php

namespace Laminas\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Pdo;
use Laminas\Db\Adapter\Driver\Pgsql;
use Laminas\Db\Adapter\Exception;
use PgSql\Connection as PgSqlConnection;

use function get_resource_type;
use function implode;
use function in_array;
use function is_resource;
use function pg_escape_string;
use function str_replace;

class Postgresql extends AbstractPlatform
{
    /**
     * Overrides value from AbstractPlatform to use proper escaping for Postgres
     *
     * @var string
     */
    protected $quoteIdentifierTo = '""';

    /** @var null|resource|\PDO|Pdo\Pdo|Pgsql\Pgsql */
    protected $driver;

    /** @var string[] */
    private $knownPgsqlResources = [
        'pgsql link',
        'pgsql link persistent',
    ];

    /**
     * @param null|\Laminas\Db\Adapter\Driver\Pgsql\Pgsql|\Laminas\Db\Adapter\Driver\Pdo\Pdo|resource|\PDO $driver
     */
    public function __construct($driver = null)
    {
        if ($driver) {
            $this->setDriver($driver);
        }
    }

    /**
     * @param Pgsql\Pgsql|Pdo\Pdo|resource|\PDO $driver
     * @return self Provides a fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function setDriver($driver)
    {
        if (
            $driver instanceof Pgsql\Pgsql
            || ($driver instanceof Pdo\Pdo && $driver->getDatabasePlatformName() === 'Postgresql')
            || $driver instanceof PgSqlConnection // PHP 8.1+
            || (is_resource($driver) && in_array(get_resource_type($driver), $this->knownPgsqlResources, true))
            || ($driver instanceof \PDO && $driver->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'pgsql')
        ) {
            $this->driver = $driver;
            return $this;
        }

        throw new Exception\InvalidArgumentException(
            '$driver must be a Pgsql or Postgresql PDO Laminas\Db\Adapter\Driver, pgsql link resource'
            . ' or Postgresql PDO instance'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'PostgreSQL';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifierChain($identifierChain)
    {
        return '"' . implode('"."', (array) str_replace('"', '""', $identifierChain)) . '"';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue($value)
    {
        $quotedViaDriverValue = $this->quoteViaDriver($value);

        return $quotedViaDriverValue ?? 'E' . parent::quoteValue($value);
    }

    /**
     * {@inheritDoc}
     *
     * @param scalar $value
     * @return string
     */
    public function quoteTrustedValue($value)
    {
        $quotedViaDriverValue = $this->quoteViaDriver($value);

        if ($quotedViaDriverValue === null) {
            return 'E' . parent::quoteTrustedValue($value);
        }

        return $quotedViaDriverValue;
    }

    /**
     * @param  string $value
     * @return string|null
     */
    protected function quoteViaDriver($value)
    {
        $resource = $this->driver instanceof DriverInterface
            ? $this->driver->getConnection()->getResource()
            : $this->driver;

        if ($resource instanceof PgSqlConnection || is_resource($resource)) {
            return '\'' . pg_escape_string($resource, $value) . '\'';
        }

        if ($resource instanceof \PDO) {
            return $resource->quote($value);
        }

        return null;
    }
}
