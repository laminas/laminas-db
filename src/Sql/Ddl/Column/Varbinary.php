<?php

declare(strict_types=1);

namespace Laminas\Db\Sql\Ddl\Column;

class Varbinary extends AbstractLengthColumn
{
    /** @var string */
    protected $type = 'VARBINARY';
}
