<?php

namespace Laminas\Db\Adapter\Platform;

use function addcslashes;
use function trigger_error;

class Sql92 extends AbstractPlatform
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'SQL92';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue($value)
    {
        trigger_error(
            'Attempting to quote a value without specific driver level support'
            . ' can introduce security vulnerabilities in a production environment.'
        );
        return '\'' . addcslashes($value, "\x00\n\r\\'\"\x1a") . '\'';
    }
}
