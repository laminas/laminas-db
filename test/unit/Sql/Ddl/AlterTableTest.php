<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl;

use Laminas\Db\Sql\Ddl\AlterTable;
use Laminas\Db\Sql\Ddl\Column;
use Laminas\Db\Sql\Ddl\Constraint;
use Laminas\Db\Sql\TableIdentifier;
use PHPUnit\Framework\TestCase;

class AlterTableTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\AlterTable::setTable
     */
    public function testSetTable()
    {
        $at = new AlterTable();
        self::assertEquals('', $at->getRawState('table'));
        self::assertSame($at, $at->setTable('test'));
        self::assertEquals('test', $at->getRawState('table'));
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\AlterTable::addColumn
     */
    public function testAddColumn()
    {
        $at = new AlterTable();
        /** @var \Laminas\Db\Sql\Ddl\Column\ColumnInterface $colMock */
        $colMock = $this->getMockBuilder('Laminas\Db\Sql\Ddl\Column\ColumnInterface')->getMock();
        self::assertSame($at, $at->addColumn($colMock));
        self::assertEquals([$colMock], $at->getRawState($at::ADD_COLUMNS));
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\AlterTable::changeColumn
     */
    public function testChangeColumn()
    {
        $at = new AlterTable();
        /** @var \Laminas\Db\Sql\Ddl\Column\ColumnInterface $colMock */
        $colMock = $this->getMockBuilder('Laminas\Db\Sql\Ddl\Column\ColumnInterface')->getMock();
        self::assertSame($at, $at->changeColumn('newname', $colMock));
        self::assertEquals(['newname' => $colMock], $at->getRawState($at::CHANGE_COLUMNS));
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\AlterTable::dropColumn
     */
    public function testDropColumn()
    {
        $at = new AlterTable();
        self::assertSame($at, $at->dropColumn('foo'));
        self::assertEquals(['foo'], $at->getRawState($at::DROP_COLUMNS));
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\AlterTable::dropConstraint
     */
    public function testDropConstraint()
    {
        $at = new AlterTable();
        self::assertSame($at, $at->dropConstraint('foo'));
        self::assertEquals(['foo'], $at->getRawState($at::DROP_CONSTRAINTS));
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\AlterTable::addConstraint
     */
    public function testAddConstraint()
    {
        $at = new AlterTable();
        /** @var \Laminas\Db\Sql\Ddl\Constraint\ConstraintInterface $conMock */
        $conMock = $this->getMockBuilder('Laminas\Db\Sql\Ddl\Constraint\ConstraintInterface')->getMock();
        self::assertSame($at, $at->addConstraint($conMock));
        self::assertEquals([$conMock], $at->getRawState($at::ADD_CONSTRAINTS));
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\AlterTable::getSqlString
     * @todo   Implement testGetSqlString().
     */
    public function testGetSqlString()
    {
        $at = new AlterTable('foo');
        $at->addColumn(new Column\Varchar('another', 255));
        $at->changeColumn('name', new Column\Varchar('new_name', 50));
        $at->dropColumn('foo');
        $at->addConstraint(new Constraint\ForeignKey('my_fk', 'other_id', 'other_table', 'id', 'CASCADE', 'CASCADE'));
        $at->dropConstraint('my_index');
        $expected = <<<EOS
ALTER TABLE "foo"
 ADD COLUMN "another" VARCHAR(255) NOT NULL,
 CHANGE COLUMN "name" "new_name" VARCHAR(50) NOT NULL,
 DROP COLUMN "foo",
 ADD CONSTRAINT "my_fk" FOREIGN KEY ("other_id") REFERENCES "other_table" ("id") ON DELETE CASCADE ON UPDATE CASCADE,
 DROP CONSTRAINT "my_index"
EOS;

        $actual = $at->getSqlString();
        self::assertEquals(
            str_replace(["\r", "\n"], "", $expected),
            str_replace(["\r", "\n"], "", $actual)
        );

        $at = new AlterTable(new TableIdentifier('foo'));
        $at->addColumn(new Column\Column('bar'));
        $this->assertEquals("ALTER TABLE \"foo\"\n ADD COLUMN \"bar\" INTEGER NOT NULL", $at->getSqlString());

        $at = new AlterTable(new TableIdentifier('bar', 'foo'));
        $at->addColumn(new Column\Column('baz'));
        $this->assertEquals("ALTER TABLE \"foo\".\"bar\"\n ADD COLUMN \"baz\" INTEGER NOT NULL", $at->getSqlString());
    }
}
