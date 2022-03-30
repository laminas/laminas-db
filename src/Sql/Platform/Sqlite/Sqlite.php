<?php

namespace Laminas\Db\Sql\Platform\Sqlite;

use Laminas\Db\Sql\Platform\AbstractPlatform;
use Laminas\Db\Sql\Select;

class Sqlite extends AbstractPlatform
{
    /**
     * Constructor
     *
     * Registers the type decorator.
     */
    public function __construct()
    {
        $this->setTypeDecorator(Select::class, new SelectDecorator());
    }
}
