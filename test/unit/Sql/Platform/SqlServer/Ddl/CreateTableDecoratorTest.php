<?php

namespace LaminasTest\Db\Sql\Platform\SqlServer\Ddl;

use Laminas\Db\Sql\Ddl\Column\Column;
use Laminas\Db\Sql\Ddl\CreateTable;
use Laminas\Db\Sql\Platform\SqlServer\Ddl\CreateTableDecorator;
use PHPUnit\Framework\TestCase;

class CreateTableDecoratorTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Platform\SqlServer\Ddl\CreateTableDecorator::getSqlString
     */
    public function testGetSqlString()
    {
        $ctd = new CreateTableDecorator();

        $ct = new CreateTable('foo');
        self::assertEquals("CREATE TABLE \"foo\" ( \n)", $ctd->setSubject($ct)->getSqlString());

        $ct = new CreateTable('foo', true);
        self::assertEquals("CREATE TABLE \"#foo\" ( \n)", $ctd->setSubject($ct)->getSqlString());

        $ct = new CreateTable('foo');
        $ct->addColumn(new Column('bar'));
        self::assertEquals(
            "CREATE TABLE \"foo\" ( \n    \"bar\" INTEGER NOT NULL \n)",
            $ctd->setSubject($ct)->getSqlString()
        );

        $ct = new CreateTable('foo', true);
        $ct->addColumn(new Column('bar'));
        self::assertEquals(
            "CREATE TABLE \"#foo\" ( \n    \"bar\" INTEGER NOT NULL \n)",
            $ctd->setSubject($ct)->getSqlString()
        );
    }
}
