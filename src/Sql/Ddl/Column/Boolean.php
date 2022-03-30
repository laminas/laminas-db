<?php

namespace Laminas\Db\Sql\Ddl\Column;

class Boolean extends Column
{
    /** @var string */
    protected $type = 'BOOLEAN';

    /**
     * {@inheritDoc}
     */
    protected $isNullable = false;

    /**
     * {@inheritDoc}
     */
    public function setNullable($nullable)
    {
        return parent::setNullable(false);
    }
}
