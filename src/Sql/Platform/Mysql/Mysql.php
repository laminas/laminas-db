<?php

namespace Laminas\Db\Sql\Platform\Mysql;

use Laminas\Db\Sql\Platform\AbstractPlatform;

class Mysql extends AbstractPlatform
{
    public function __construct()
    {
        $this->setTypeDecorator('Laminas\Db\Sql\Select', new SelectDecorator());
        $this->setTypeDecorator('Laminas\Db\Sql\Ddl\CreateTable', new Ddl\CreateTableDecorator());
        $this->setTypeDecorator('Laminas\Db\Sql\Ddl\AlterTable', new Ddl\AlterTableDecorator());
    }
}
