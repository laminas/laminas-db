<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Platform;

use PDO;
use Zend\Db\Adapter\Driver\DriverInterface;

class Sqlite extends AbstractPlatform
{
    /**
     * {@inheritDoc}
     */
    protected $quoteIdentifier = ['"','"'];

    /**
     * {@inheritDoc}
     */
    protected $quoteIdentifierTo = '\'';

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

    public function setDriver(DriverInterface $driver): Sqlite
    {
        $this->resource = $driver;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'SQLite';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue(string $value): string
    {
        $resource = $this->resource;

        if ($resource instanceof DriverInterface) {
            $resource = $resource->getConnection()->getResource();
        }

        if ($resource instanceof PDO) {
            return $resource->quote($value);
        }

        return parent::quoteValue($value);
    }

    /**
     * {@inheritDoc}
     */
    public function quoteTrustedValue(string $value): string
    {
        $resource = $this->resource;

        if ($resource instanceof DriverInterface) {
            $resource = $resource->getConnection()->getResource();
        }

        if ($resource instanceof PDO) {
            return $resource->quote($value);
        }

        return parent::quoteTrustedValue($value);
    }
}
