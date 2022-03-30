<?php

namespace Laminas\Db\Sql\Ddl\Column;

class Char extends AbstractLengthColumn
{
    /** @var string */
    protected $type = 'CHAR';
}
