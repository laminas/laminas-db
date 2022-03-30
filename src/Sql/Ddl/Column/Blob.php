<?php

namespace Laminas\Db\Sql\Ddl\Column;

class Blob extends AbstractLengthColumn
{
    /** @var string Change type to blob */
    protected $type = 'BLOB';
}
