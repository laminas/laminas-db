<?php

namespace Laminas\Db\Sql\Predicate;

class NotBetween extends Between
{
    /** @var string */
    protected $specification = '%1$s NOT BETWEEN %2$s AND %3$s';
}
