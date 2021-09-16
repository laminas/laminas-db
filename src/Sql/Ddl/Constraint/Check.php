<?php

namespace Laminas\Db\Sql\Ddl\Constraint;

use Laminas\Db\Sql\ExpressionInterface;

use function array_unshift;

class Check extends AbstractConstraint
{
    /** @var string|ExpressionInterface */
    protected $expression;

    /**
     * {@inheritDoc}
     */
    protected $specification = 'CHECK (%s)';

    /**
     * @param string|ExpressionInterface $expression
     * @param  null|string $name
     */
    public function __construct($expression, $name)
    {
        $this->expression = $expression;
        $this->name       = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpressionData()
    {
        $newSpecTypes = [self::TYPE_LITERAL];
        $values       = [$this->expression];
        $newSpec      = '';

        if ($this->name) {
            $newSpec .= $this->namedSpecification;

            array_unshift($values, $this->name);
            array_unshift($newSpecTypes, self::TYPE_IDENTIFIER);
        }

        return [
            [
                $newSpec . $this->specification,
                $values,
                $newSpecTypes,
            ],
        ];
    }
}
