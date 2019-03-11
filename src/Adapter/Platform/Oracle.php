<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Platform;

use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Pdo\Pdo;
use Zend\Db\Adapter\Exception\InvalidArgumentException;

class Oracle extends AbstractPlatform
{
    /**
     * @var DriverInterface
     */
    protected $resource = null;

    public function __construct(array $options = [], DriverInterface $driver = null)
    {
        if (isset($options['quote_identifiers'])
            && ($options['quote_identifiers'] == false
            || $options['quote_identifiers'] === 'false')
        ) {
            $this->quoteIdentifiers = false;
        }

        if ($driver) {
            $this->setDriver($driver);
        }
    }

    /**
     * @return self Provides a fluent interface
     */
    public function setDriver(DriverInterface $driver): Oracle
    {
        // if ($driver instanceof Oci8
        //     || ($driver instanceof Pdo && $driver->getDatabasePlatformName() == 'Oracle')
        //     || ($driver instanceof Pdo && $driver->getDatabasePlatformName() == 'Sqlite')
        //     || ($driver instanceof \oci8)
        //     || ($driver instanceof PDO && $driver->getAttribute(PDO::ATTR_DRIVER_NAME) == 'oci')
        // ) {
        $this->resource = $driver;
        return $this;
    }

    public function getDriver(): DriverInterface
    {
        return $this->resource;
    }

    public function getName(): string
    {
        return 'Oracle';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifierChain(array $identifierChain): string
    {
        if ($this->quoteIdentifiers === false) {
            return implode('.', (array) $identifierChain);
        }

        return '"' . implode('"."', (array) str_replace('"', '\\"', $identifierChain)) . '"';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue(string $value): string
    {
        $resource = $this->resource->getConnection()->getResource();
        if ($resource instanceof Pdo) {
            return $resource->quote($value);
        }

        if (get_resource_type($resource) == 'oci8 connection'
            || get_resource_type($resource) == 'oci8 persistent connection'
        ) {
            return "'" . addcslashes(str_replace("'", "''", $value), "\x00\n\r\"\x1a") . "'";
        }

        trigger_error(
            'Attempting to quote a value in ' . __CLASS__ . ' without extension/driver support '
            . 'can introduce security vulnerabilities in a production environment.'
        );

        return "'" . addcslashes(str_replace("'", "''", $value), "\x00\n\r\"\x1a") . "'";
    }

    /**
     * {@inheritDoc}
     */
    public function quoteTrustedValue(string $value): string
    {
        return "'" . addcslashes(str_replace('\'', '\'\'', $value), "\x00\n\r\"\x1a") . "'";
    }
}
