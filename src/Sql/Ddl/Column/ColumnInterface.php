<?php

namespace Laminas\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\ExpressionInterface;

/**
 * Interface ColumnInterface describes the protocol on how Column objects interact
 */
interface ColumnInterface extends ExpressionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return bool
     */
    public function isNullable();

    /**
     * @return null|string|int
     */
    public function getDefault();

    /**
     * @return array
     */
    public function getOptions();
}
