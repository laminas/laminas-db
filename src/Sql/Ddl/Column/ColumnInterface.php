<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\ExpressionInterface;

interface ColumnInterface extends ExpressionInterface
{
    public function getName();
    public function isNullable();
    public function getDefault();
    public function getOptions();
}
