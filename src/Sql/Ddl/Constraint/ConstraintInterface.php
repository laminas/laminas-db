<?php

declare(strict_types=1);

namespace Laminas\Db\Sql\Ddl\Constraint;

use Laminas\Db\Sql\ExpressionInterface;

interface ConstraintInterface extends ExpressionInterface
{
    public function getColumns();
}
