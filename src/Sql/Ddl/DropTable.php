<?php

namespace Laminas\Db\Sql\Ddl;

use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Sql\AbstractSql;
use Laminas\Db\Sql\TableIdentifier;

class DropTable extends AbstractSql implements SqlInterface
{
    public const TABLE = 'table';

    /** @var array */
    protected $specifications = [
        self::TABLE => 'DROP TABLE %1$s',
    ];

    /** @var string */
    protected $table = '';

    /**
     * @param string|TableIdentifier $table
     */
    public function __construct($table = '')
    {
        $this->table = $table;
    }

    /** @return string[] */
    protected function processTable(?PlatformInterface $adapterPlatform = null)
    {
        return [$this->resolveTable($this->table, $adapterPlatform)];
    }
}
