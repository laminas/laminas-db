<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Ddl\Column;

class Boolean extends Column
{
    /**
     * @var string specification
     */
    protected $specification = '%s TINYINT NOT NULL';

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        $spec   = $this->specification;
        $params = array($this->name);
        $types  = array(self::TYPE_IDENTIFIER);

        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}
