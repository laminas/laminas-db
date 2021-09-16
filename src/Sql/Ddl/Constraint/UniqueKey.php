<?php

namespace Laminas\Db\Sql\Ddl\Constraint;

class UniqueKey extends AbstractConstraint
{
    /** @var string */
    protected $specification = 'UNIQUE';
}
