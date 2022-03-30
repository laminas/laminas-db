<?php

namespace Laminas\Db\Sql\Ddl\Constraint;

class PrimaryKey extends AbstractConstraint
{
    /** @var string */
    protected $specification = 'PRIMARY KEY';
}
