<?php

declare(strict_types=1);

namespace Laminas\Db\Sql\Ddl\Column;

class Text extends AbstractLengthColumn
{
    /** @var string */
    protected $type = 'TEXT';
}
