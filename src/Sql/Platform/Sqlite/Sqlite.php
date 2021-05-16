<?php

namespace Laminas\Db\Sql\Platform\Sqlite;

use Laminas\Db\Sql\Platform\AbstractPlatform;

class Sqlite extends AbstractPlatform
{
    /**
     * Constructor
     *
     * Registers the type decorator.
     */
    public function __construct()
    {
        $this->setTypeDecorator('Laminas\Db\Sql\Select', new SelectDecorator());
    }
}
