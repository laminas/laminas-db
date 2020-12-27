<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Platform\Mysql\Ddl;

use Laminas\Db\Adapter\Platform\Mysql;
use Laminas\Db\Metadata\Object\ConstraintObject;
use Laminas\Db\Sql\Ddl\AlterTable;
use Laminas\Db\Sql\Ddl\Column\Column;
use Laminas\Db\Sql\Ddl\Constraint\PrimaryKey;
use Laminas\Db\Sql\Ddl\Constraint\UniqueKey;
use Laminas\Db\Sql\Platform\Mysql\Ddl\AlterTableDecorator;
use PHPUnit\Framework\TestCase;

class AlterTableDecoratorTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Platform\Mysql\Ddl\AlterTableDecorator::setSubject
     */
    public function testSetSubject()
    {
        $ctd = new AlterTableDecorator();
        $ct = new AlterTable;
        self::assertSame($ctd, $ctd->setSubject($ct));
    }

    /**
     * @covers \Laminas\Db\Sql\Platform\Mysql\Ddl\AlterTableDecorator::getSqlString
     */
    public function testGetSqlString()
    {
        $ctd = new AlterTableDecorator();
        $ct = new AlterTable('foo');
        $ctd->setSubject($ct);

        $col = new Column('bar');
        $col->setOption('zerofill', true);
        $col->setOption('unsigned', true);
        $col->setOption('identity', true);
        $col->setOption('comment', 'baz');
        $col->setOption('after', 'bar');
        $col->addConstraint(new PrimaryKey());
        $ct->addColumn($col);

        $fk = new ConstraintObject('my_fk', null);
        $fk->setType('FOREIGN KEY');
        $ct->dropConstraint($fk);

        $ct->dropConstraint(new UniqueKey(null, 'my_unique_index'));

        $this->assertEquals(
            "ALTER TABLE `foo`\n"
            ." ADD COLUMN `bar` INTEGER UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'baz'"
            ." AFTER `bar`,\n"
            ."    DROP FOREIGN KEY `my_fk`,\n"
            ." DROP KEY `my_unique_index`",
            @$ctd->getSqlString(new Mysql())
        );
    }
}
