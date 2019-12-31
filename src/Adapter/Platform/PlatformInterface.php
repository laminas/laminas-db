<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Platform;

interface PlatformInterface
{
    public function getName(): string;

    public function getQuoteIdentifierSymbol(): string;

    public function quoteIdentifier(string $identifier): string;

    public function quoteIdentifierChain(array $identifierChain): string;

    public function getQuoteValueSymbol(): string;

    public function quoteValue(string $value): string;

    public function quoteTrustedValue(string $value): string;

    public function quoteValueList(array $valueList): string;

    public function getIdentifierSeparator(): string;

    public function quoteIdentifierInFragment(string $identifier, array $additionalSafeWords = []): string;
}
