<?php

namespace Laminas\Db\Sql\Platform\Oracle;

use Laminas\Db\Sql\Platform\AbstractPlatform;

class Oracle extends AbstractPlatform
{
    public function __construct(SelectDecorator $selectDecorator = null)
    {
        $this->setTypeDecorator('Laminas\Db\Sql\Select', ($selectDecorator) ?: new SelectDecorator());
    }
}
