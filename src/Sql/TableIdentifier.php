<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql;

/**
 *
 */
class TableIdentifier
{

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $schema;

    /**
     * @param string $table
     * @param string $schema
     */
    public function __construct($table, $schema = null)
    {
        $this->table = $table;
        $this->schema = $schema;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return bool
     */
    public function hasSchema()
    {
        return ($this->schema != null);
    }

    /**
     * @param $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return null|string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    public function getTableAndSchema()
    {
        return array($this->table, $this->schema);
    }
}
