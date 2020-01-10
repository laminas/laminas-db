<?php
/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Oci8\Oci8;
use Laminas\Db\Adapter\Driver\Pdo\Pdo;
use Laminas\Db\Adapter\Exception\InvalidArgumentException;

class Oracle extends AbstractPlatform
{
    /**
     * @var null|Pdo|Oci8
     */
    protected $driver = null;

    /**
     * @param array $options
     * @param null|Oci8|Pdo $driver
     */
    public function __construct($options = [], $driver = null)
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
     * @param Pdo|Oci8 $driver
     * @return self Provides a fluent interface
     * @throws InvalidArgumentException
     */
    public function setDriver($driver)
    {
        if ($driver instanceof Oci8
            || ($driver instanceof Pdo && $driver->getDatabasePlatformName() == 'Oracle')
            || ($driver instanceof Pdo && $driver->getDatabasePlatformName() == 'Sqlite')
            || ($driver instanceof \oci8)
        ) {
            $this->driver = $driver;
            return $this;
        }

        throw new InvalidArgumentException(
            '$driver must be a Oci8, Oracle PDO Laminas\Db\Adapter\Driver or Oci8 instance'
        );
    }

    /**
     * @return null|Pdo|Oci8
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Oracle';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifierChain($identifierChain)
    {
        if ($this->quoteIdentifiers === false) {
            return implode('.', (array) $identifierChain);
        }

        return '"' . implode('"."', (array) str_replace('"', '\\"', $identifierChain)) . '"';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue($value)
    {
        if ($this->driver instanceof DriverInterface) {
            $resource = $this->driver->getConnection()->getResource();
        } else {
            $resource = $this->driver;
        }

        if ($resource) {
            if ($resource instanceof PDO) {
                return $resource->quote($value);
            }

            if (get_resource_type($resource) == 'oci8 connection'
                || get_resource_type($resource) == 'oci8 persistent connection'
            ) {
                return "'" . addcslashes(str_replace("'", "''", $value), "\x00\n\r\"\x1a") . "'";
            }
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
    public function quoteTrustedValue($value)
    {
        return "'" . addcslashes(str_replace('\'', '\'\'', $value), "\x00\n\r\"\x1a") . "'";
    }
}
