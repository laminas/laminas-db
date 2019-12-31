<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Ddl\Constraint;

abstract class AbstractConstraint implements ConstraintInterface
{
    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @param null|string|array $columns
     */
    public function __construct($columns = null)
    {
        (!$columns) ?: $this->setColumns($columns);
    }

    /**
     * @param  null|string|array $columns
     * @return self
     */
    public function setColumns($columns)
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }

        $this->columns = $columns;
        return $this;
    }

    /**
     * @param  string $column
     * @return self
     */
    public function addColumn($column)
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }
}
