<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Platform\Mysql\Ddl;

use Laminas\Db\Adapter\Platform\Mysql;
use Laminas\Db\Sql\Ddl\Column\Column;
use Laminas\Db\Sql\Ddl\Constraint\PrimaryKey;
use Laminas\Db\Sql\Ddl\CreateTable;
use Laminas\Db\Sql\Platform\Mysql\Ddl\CreateTableDecorator;
use PHPUnit\Framework\TestCase;

class CreateTableDecoratorTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Platform\Mysql\Ddl\CreateTableDecorator::setSubject
     */
    public function testSetSubject()
    {
        $ctd = new CreateTableDecorator();
        $ct = new CreateTable;
        self::assertSame($ctd, $ctd->setSubject($ct));
    }

    /**
     * @covers \Laminas\Db\Sql\Platform\Mysql\Ddl\CreateTableDecorator::getSqlString
     */
    public function testGetSqlString()
    {
        $ctd = new CreateTableDecorator();
        $ct = new CreateTable('foo');
        $ctd->setSubject($ct);

        $col = new Column('bar');
        $col->setOption('zerofill', true);
        $col->setOption('unsigned', true);
        $col->setOption('identity', true);
        $col->setOption('column-format', 'FIXED');
        $col->setOption('storage', 'memory');
        $col->setOption('comment', 'baz');
        $col->addConstraint(new PrimaryKey());
        $ct->addColumn($col);

        self::assertEquals(
            // @codingStandardsIgnoreStart
            "CREATE TABLE `foo` ( \n    `bar` INTEGER UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'baz' COLUMN_FORMAT FIXED STORAGE MEMORY \n)",
            // @codingStandardsIgnoreEnd
            @$ctd->getSqlString(new Mysql())
        );
    }
}
