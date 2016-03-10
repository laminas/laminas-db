<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql;

class Join implements \Iterator, \Countable
{
    const JOIN_INNER = 'inner';
    const JOIN_OUTER = 'outer';
    const JOIN_LEFT = 'left';
    const JOIN_RIGHT = 'right';
    const JOIN_RIGHT_OUTER = 'right outer';
    const JOIN_LEFT_OUTER  = 'left outer';

    private $position = 0;

    protected $joins = [];

    public function __construct()
    {
        $this->position = 0;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->joins[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->joins[$this->position]);
    }

    /**
     * @return mixed
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * @param mixed $join
     */
    public function join($name, $on, $columns = Select::SQL_STAR, $type = Join::JOIN_INNER)
    {
        if (is_array($name) && (!is_string(key($name)) || count($name) !== 1)) {
            throw new Exception\InvalidArgumentException(
                sprintf("join() expects '%s' as an array is a single element associative array", array_shift($name))
            );
        }

        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $this->joins[] = [
            'name'    => $name,
            'on'      => $on,
            'columns' => $columns ? $columns : Select::SQL_STAR,
            'type'    => $type ? $type : Join::JOIN_INNER
        ];

        return $this;
    }

    public function reset()
    {
        $this->joins = [];

        return $this;
    }

    /**
     * Get count of attached predicates
     *
     * @return int
     */
    public function count()
    {
        return count($this->joins);
    }
}
