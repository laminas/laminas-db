<?php

declare(strict_types=1);

namespace Laminas\Db\Sql\Ddl\Column;

class Timestamp extends AbstractTimestampColumn
{
    /** @var string */
    protected $type = 'TIMESTAMP';
}
