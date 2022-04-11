<?php

namespace Laminas\Db\Sql;

use function str_replace;

class Literal implements ExpressionInterface
{
    /** @var string */
    protected $literal = '';

    /**
     * @param string $literal
     */
    public function __construct($literal = '')
    {
        $this->literal = $literal;
    }

    /**
     * @param string $literal
     * @return $this Provides a fluent interface
     */
    public function setLiteral($literal)
    {
        $this->literal = $literal;
        return $this;
    }

    /**
     * @return string
     */
    public function getLiteral()
    {
        return $this->literal;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        return [
            [
                str_replace('%', '%%', $this->literal),
                [],
                [],
            ],
        ];
    }
}
