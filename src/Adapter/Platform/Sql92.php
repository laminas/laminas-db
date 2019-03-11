<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Platform;

class Sql92 extends AbstractPlatform
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'SQL92';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue(string $value): string
    {
        trigger_error(
            'Attempting to quote a value without specific driver level support'
            . ' can introduce security vulnerabilities in a production environment.'
        );
        return '\'' . addcslashes($value, "\x00\n\r\\'\"\x1a") . '\'';
    }
}
