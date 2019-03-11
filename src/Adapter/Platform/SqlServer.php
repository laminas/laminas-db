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
