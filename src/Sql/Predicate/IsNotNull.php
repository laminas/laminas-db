<?php

declare(strict_types=1);

namespace Laminas\Db\Sql\Predicate;

class IsNotNull extends IsNull
{
    /** @var string */
    protected $specification = '%1$s IS NOT NULL';
}
