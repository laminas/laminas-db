<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Platform;

Use PDO;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Pgsql;
use Zend\Db\Adapter\Exception;

class Postgresql extends AbstractPlatform
{
    /**
     * @var string
     */
    protected $quoteIdentifierTo = '""';

    /**
     * @var DriverInterface
     */
    protected $resource = null;

    public function __construct(DriverInterface $driver = null)
    {
        if ($driver) {
            $this->setDriver($driver);
        }
    }

    /**
     * @return self Provides a fluent interface
     */
    public function setDriver(DriverInterface $driver): Postgresql
    {
        // if ($driver instanceof Pgsql\Pgsql
        //     || ($driver instanceof Pdo\Pdo && $driver->getDatabasePlatformName() == 'Postgresql')
        //     || (is_resource($driver) && (in_array(get_resource_type($driver), ['pgsql link', 'pgsql link persistent'])))
        //     || ($driver instanceof \PDO && $driver->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'pgsql')
        // ) {
        $this->resource = $driver;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'PostgreSQL';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifierChain(array $identifierChain): string
    {
        return '"' . implode('"."', (array) str_replace('"', '""', $identifierChain)) . '"';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue(string $value): string
    {
        $resource = $this->resource->getConnection()->getResource();
        if (is_resource($resource)) {
            return '\'' . pg_escape_string($resource, $value) . '\'';
        }
        if ($resource instanceof PDO) {
            return $resource->quote($value);
        }
        return 'E' . parent::quoteValue($value);
    }

    /**
     * {@inheritDoc}
     */
    public function quoteTrustedValue(string $value): string
    {
        $resource = $this->resource->getConnection()->getResource();
        if (is_resource($resource)) {
            return '\'' . pg_escape_string($resource, $value) . '\'';
        }
        if ($resource instanceof PDO) {
            return $resource->quote($value);
        }
        return 'E' . parent::quoteTrustedValue($value);
    }
}
