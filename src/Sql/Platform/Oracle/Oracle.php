<?php

namespace Laminas\Db\Sql\Platform\Oracle;

use Laminas\Db\Sql\Platform\AbstractPlatform;
use Laminas\Db\Sql\Select;

class Oracle extends AbstractPlatform
{
    public function __construct(?SelectDecorator $selectDecorator = null)
    {
        $this->setTypeDecorator(Select::class, $selectDecorator ?: new SelectDecorator());
    }
}
