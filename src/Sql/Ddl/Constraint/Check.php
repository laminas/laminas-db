<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Ddl\Constraint;

class Check extends AbstractConstraint
{
    /**
     * @var string|\Laminas\Db\Sql\ExpressionInterface
     */
    protected $expression;

    /**
     * @var string
     */
    protected $specification = 'CONSTRAINT %s CHECK (%s)';

    /**
     * @param  string|\Laminas\Db\Sql\ExpressionInterface $expression
     * @param  null|string $name
     */
    public function __construct($expression, $name)
    {
        $this->expression = $expression;
        $this->name       = $name;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        return array(array(
            $this->specification,
            array($this->name, $this->expression),
            array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL),
        ));
    }
}
