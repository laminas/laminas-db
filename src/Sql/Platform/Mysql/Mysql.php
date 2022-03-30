<?php

namespace Laminas\Db\Sql\Platform\Mysql;

use Laminas\Db\Sql\Ddl\AlterTable;
use Laminas\Db\Sql\Ddl\CreateTable;
use Laminas\Db\Sql\Platform\AbstractPlatform;
use Laminas\Db\Sql\Select;

class Mysql extends AbstractPlatform
{
    public function __construct()
    {
        $this->setTypeDecorator(Select::class, new SelectDecorator());
        $this->setTypeDecorator(CreateTable::class, new Ddl\CreateTableDecorator());
        $this->setTypeDecorator(AlterTable::class, new Ddl\AlterTableDecorator());
    }
}
