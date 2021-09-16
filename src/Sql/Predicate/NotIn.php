<?php

namespace Laminas\Db\Sql\Predicate;

class NotIn extends In
{
    /** @var string */
    protected $specification = '%s NOT IN %s';
}
