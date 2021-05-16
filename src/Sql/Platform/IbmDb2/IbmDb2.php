<?php

namespace Laminas\Db\Sql\Platform\IbmDb2;

use Laminas\Db\Sql\Platform\AbstractPlatform;

class IbmDb2 extends AbstractPlatform
{
    /**
     * @param SelectDecorator $selectDecorator
     */
    public function __construct(SelectDecorator $selectDecorator = null)
    {
        $this->setTypeDecorator('Laminas\Db\Sql\Select', ($selectDecorator) ?: new SelectDecorator());
    }
}
