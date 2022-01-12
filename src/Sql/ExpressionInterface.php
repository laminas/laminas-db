<?php

namespace Laminas\Db\Sql;

interface ExpressionInterface
{
    public const TYPE_IDENTIFIER = 'identifier';
    public const TYPE_VALUE      = 'value';
    public const TYPE_LITERAL    = 'literal';
    public const TYPE_SELECT     = 'select';

    /**
     * @abstract
     * @return array of array|string should return an array in the format:
     *
     * array (
     *    // a sprintf formatted string
     *    string $specification,
     *
     *    // the values for the above sprintf formatted string
     *    array $values,
     *
     *    // an array of equal length of the $values array, with either TYPE_IDENTIFIER or TYPE_VALUE for each value
     *    array $types,
     * )
     */
    public function getExpressionData();
}
