<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Platform\Mysql\Ddl;

use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Sql\Ddl\CreateTable;
use Laminas\Db\Sql\Platform\PlatformDecoratorInterface;

class CreateTableDecorator extends CreateTable implements PlatformDecoratorInterface
{
    /**
     * @var CreateTable
     */
    protected $createTable;

    /**
     * @param CreateTable $subject
     */
    public function setSubject($subject)
    {
        $this->createTable = $subject;
    }

    /**
     * @param  null|PlatformInterface $platform
     * @return string
     */
    public function getSqlString(PlatformInterface $platform = null)
    {
        // localize variables
        foreach (get_object_vars($this->createTable) as $name => $value) {
            $this->{$name} = $value;
        }
        return parent::getSqlString($platform);
    }

    protected function processColumns(PlatformInterface $platform = null)
    {
        $sqls = array();
        foreach ($this->columns as $i => $column) {
            $stmtContainer = $this->processExpression($column, $platform);
            $sql           = $stmtContainer->getSql();
            $columnOptions = $column->getOptions();

            foreach ($columnOptions as $coName => $coValue) {
                switch (strtolower(str_replace(array('-', '_', ' '), '', $coName))) {
                    case 'identity':
                    case 'serial':
                    case 'autoincrement':
                        $sql .= ' AUTO_INCREMENT';
                        break;
                    /*
                    case 'primary':
                    case 'primarykey':
                        $sql .= ' PRIMARY KEY';
                        break;
                    case 'unique':
                    case 'uniquekey':
                        $sql .= ' UNIQUE KEY';
                        break;
                    */
                    case 'comment':
                        $sql .= ' COMMENT \'' . $coValue . '\'';
                        break;
                    case 'columnformat':
                    case 'format':
                        $sql .= ' COLUMN_FORMAT ' . strtoupper($coValue);
                        break;
                    case 'storage':
                        $sql .= ' STORAGE ' . strtoupper($coValue);
                        break;
                }
            }
            $stmtContainer->setSql($sql);
            $sqls[$i] = $stmtContainer;
        }
        return array($sqls);
    }
}
