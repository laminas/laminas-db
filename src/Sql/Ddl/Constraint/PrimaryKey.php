<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Ddl\Constraint;

class PrimaryKey extends AbstractConstraint
{
    /**
     * @var string
     */
    protected $specification = 'PRIMARY KEY (%s)';

    /**
     * @return array
     */
    public function getExpressionData()
    {
        $colCount     = count($this->columns);
        $newSpecParts = array_fill(0, $colCount, '%s');
        $newSpecTypes = array_fill(0, $colCount, self::TYPE_IDENTIFIER);

        $newSpec = sprintf($this->specification, implode(', ', $newSpecParts));

        return array(array(
            $newSpec,
            $this->columns,
            $newSpecTypes,
        ));
    }
}
