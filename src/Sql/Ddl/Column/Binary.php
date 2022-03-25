<?php

declare(strict_types=1);

namespace Laminas\Db\Sql\Ddl\Column;

class Binary extends AbstractLengthColumn
{
    /** @var string */
    protected $type = 'BINARY';
}
