<?php

namespace Laminas\Db\Sql\Ddl\Constraint;

use Laminas\Db\Sql\ExpressionInterface;

interface ConstraintInterface extends ExpressionInterface
{
    public function getColumns();
}
