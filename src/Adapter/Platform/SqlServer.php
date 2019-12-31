<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\DriverInterface;
use PDO;

class SqlServer extends AbstractPlatform
{
    /**
     * {@inheritDoc}
     */
    protected $quoteIdentifier = ['[',']'];

    /**
     * {@inheritDoc}
     */
    protected $quoteIdentifierTo = '\\';

    /**
     * @var DriverInterface
     */
    protected $resource = null;

    public function __construct(DriverInterface $driver = null)
    {
        if (null !== $driver) {
            $this->setDriver($driver);
        }
    }

    public function setDriver(DriverInterface $driver): SqlServer
    {
        $this->resource = $driver;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'SQLServer';
    }

    /**
     * {@inheritDoc}
     */
    public function getQuoteIdentifierSymbol(): string
    {
        return $this->quoteIdentifier;
    }

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifierChain(array $identifierChain): string
    {
        return '[' . implode('].[', (array) $identifierChain) . ']';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue(string $value): string
    {
        $resource = $this->resource->getConnection()->getResource();
        if ($resource instanceof PDO) {
            return $resource->quote($value);
        }
        trigger_error(
            'Attempting to quote a value in ' . __CLASS__ . ' without extension/driver support '
                . 'can introduce security vulnerabilities in a production environment.'
        );

        return '\'' . str_replace('\'', '\'\'', addcslashes($value, "\000\032")) . '\'';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteTrustedValue(string $value): string
    {
        $resource = $this->resource->getConnection()->getResource();
        if ($resource instanceof PDO) {
            return $resource->quote($value);
        }
        return '\'' . str_replace('\'', '\'\'', $value) . '\'';
    }
}
