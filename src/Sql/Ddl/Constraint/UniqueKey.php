<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Ddl\Constraint;

class UniqueKey extends AbstractConstraint
{
    /**
     * @var string
     */
    protected $specification = 'CONSTRAINT UNIQUE KEY %s(...)';

    /**
     * @param  string $column
     * @param  null|string $name
     */
    public function __construct($column, $name = null)
    {
        $this->setColumns($column);
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        $colCount = count($this->columns);

        $values   = array();
        $values[] = ($this->name) ? $this->name : '';

        $newSpecTypes = array(self::TYPE_IDENTIFIER);
        $newSpecParts = array();

        for ($i = 0; $i < $colCount; $i++) {
            $newSpecParts[] = '%s';
            $newSpecTypes[] = self::TYPE_IDENTIFIER;
        }

        $newSpec = str_replace('...', implode(', ', $newSpecParts), $this->specification);

        return array(array(
            $newSpec,
            array_merge($values, $this->columns),
            $newSpecTypes,
        ));
    }
}
