<?php

declare(strict_types=1);

namespace Laminas\Db\Sql\Ddl\Column;

class Varchar extends AbstractLengthColumn
{
    /** @var string */
    protected $type = 'VARCHAR';
}
