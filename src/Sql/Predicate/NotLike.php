<?php

namespace Laminas\Db\Sql\Predicate;

class NotLike extends Like
{
    /** @var string */
    protected $specification = '%1$s NOT LIKE %2$s';
}
