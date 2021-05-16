<?php

namespace Laminas\Db\Sql\Platform\SqlServer;

use Laminas\Db\Sql\Platform\AbstractPlatform;

class SqlServer extends AbstractPlatform
{
    public function __construct(SelectDecorator $selectDecorator = null)
    {
        $this->setTypeDecorator('Laminas\Db\Sql\Select', ($selectDecorator) ?: new SelectDecorator());
        $this->setTypeDecorator('Laminas\Db\Sql\Ddl\CreateTable', new Ddl\CreateTableDecorator());
    }
}
