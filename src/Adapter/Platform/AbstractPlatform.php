<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Platform;

abstract class AbstractPlatform implements PlatformInterface
{
    /**
     * @var string[]
     */
    protected $quoteIdentifier = ['"', '"'];

    /**
     * @var string
     */
    protected $quoteIdentifierTo = '\'';

    /**
     * @var bool
     */
    protected $quoteIdentifiers = true;

    /**
     * @var string
     */
    protected $quoteIdentifierFragmentPattern = '/([^0-9,a-z,A-Z$_:])/i';

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifierInFragment(string $identifier, array $additionalSafeWords = []): string
    {
        if (! $this->quoteIdentifiers) {
            return $identifier;
        }

        $safeWordsInt = ['*' => true, ' ' => true, '.' => true, 'as' => true];

        foreach ($additionalSafeWords as $sWord) {
            $safeWordsInt[strtolower($sWord)] = true;
        }

        $parts = preg_split(
            $this->quoteIdentifierFragmentPattern,
            $identifier,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $identifier = '';

        foreach ($parts as $part) {
            $identifier .= isset($safeWordsInt[strtolower($part)])
                ? $part
                : $this->quoteIdentifier[0]
                . str_replace($this->quoteIdentifier[0], $this->quoteIdentifierTo, $part)
                . $this->quoteIdentifier[1];
        }

        return $identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifier(string $identifier): string
    {
        if (! $this->quoteIdentifiers) {
            return $identifier;
        }

        return $this->quoteIdentifier[0]
            . str_replace($this->quoteIdentifier[0], $this->quoteIdentifierTo, $identifier)
            . $this->quoteIdentifier[1];
    }

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifierChain(array $identifierChain): string
    {
        return '"' . implode('"."', (array) str_replace('"', '\\"', $identifierChain)) . '"';
    }

    /**
     * {@inheritDoc}
     */
    public function getQuoteIdentifierSymbol(): string
    {
        return $this->quoteIdentifier[0];
    }

    /**
     * {@inheritDoc}
     */
    public function getQuoteValueSymbol(): string
    {
        return '\'';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue(string $value): string
    {
        trigger_error(
            'Attempting to quote a value in ' . get_class($this) .
            ' without extension/driver support can introduce security vulnerabilities in a production environment'
        );
        return '\'' . addcslashes((string) $value, "\x00\n\r\\'\"\x1a") . '\'';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteTrustedValue(string $value): string
    {
        return '\'' . addcslashes((string) $value, "\x00\n\r\\'\"\x1a") . '\'';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValueList(array $valueList): string
    {
        return implode(', ', array_map([$this, 'quoteValue'], (array) $valueList));
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifierSeparator(): string
    {
        return '.';
    }
}
