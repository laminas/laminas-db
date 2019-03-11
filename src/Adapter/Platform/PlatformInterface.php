<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Platform;

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
