<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Pgsql;
use Laminas\Db\Adapter\Exception;
Use PDO;

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

    public function setDriver(DriverInterface $driver): Postgresql
    {
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
