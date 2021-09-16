<?php

namespace LaminasTest\Db\Sql\Ddl;

use Laminas\Db\Sql\Ddl\Column\Column;
use Laminas\Db\Sql\Ddl\Column\ColumnInterface;
use Laminas\Db\Sql\Ddl\Constraint;
use Laminas\Db\Sql\Ddl\Constraint\ConstraintInterface;
use Laminas\Db\Sql\Ddl\CreateTable;
use Laminas\Db\Sql\TableIdentifier;
use PHPUnit\Framework\TestCase;

use function array_pop;

class CreateTableTest extends TestCase
{
    /**
     * test object construction
     *
     * @covers \Laminas\Db\Sql\Ddl\CreateTable::__construct
     */
    public function testObjectConstruction()
    {
        $ct = new CreateTable('foo', true);
        self::assertEquals('foo', $ct->getRawState($ct::TABLE));
        self::assertTrue($ct->isTemporary());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\CreateTable::setTemporary
     */
    public function testSetTemporary()
    {
        $ct = new CreateTable();
        self::assertSame($ct, $ct->setTemporary(false));
        self::assertFalse($ct->isTemporary());
        $ct->setTemporary(true);
        self::assertTrue($ct->isTemporary());
        $ct->setTemporary('yes');
        self::assertTrue($ct->isTemporary());

        self::assertStringStartsWith("CREATE TEMPORARY TABLE", $ct->getSqlString());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\CreateTable::isTemporary
     */
    public function testIsTemporary()
    {
        $ct = new CreateTable();
        self::assertFalse($ct->isTemporary());
        $ct->setTemporary(true);
        self::assertTrue($ct->isTemporary());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\CreateTable::setTable
     */
    public function testSetTable(): CreateTable
    {
        $ct = new CreateTable();
        self::assertEquals('', $ct->getRawState('table'));
        $ct->setTable('test');
        return $ct;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\CreateTable::getRawState
     * @depends testSetTable
     */
    public function testRawStateViaTable(CreateTable $ct)
    {
        self::assertEquals('test', $ct->getRawState('table'));
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\CreateTable::addColumn
     */
    public function testAddColumn(): CreateTable
    {
        $column = $this->getMockBuilder(ColumnInterface::class)->getMock();
        $ct     = new CreateTable();
        self::assertSame($ct, $ct->addColumn($column));
        return $ct;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\CreateTable::getRawState
     * @depends testAddColumn
     */
    public function testRawStateViaColumn(CreateTable $ct)
    {
        $state = $ct->getRawState('columns');
        self::assertIsArray($state);
        $column = array_pop($state);
        self::assertInstanceOf(ColumnInterface::class, $column);
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\CreateTable::addConstraint
     */
    public function testAddConstraint(): CreateTable
    {
        $constraint = $this->getMockBuilder(ConstraintInterface::class)->getMock();
        $ct         = new CreateTable();
        self::assertSame($ct, $ct->addConstraint($constraint));
        return $ct;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\CreateTable::getRawState
     * @depends testAddConstraint
     */
    public function testRawStateViaConstraint(CreateTable $ct)
    {
        $state = $ct->getRawState('constraints');
        self::assertIsArray($state);
        $constraint = array_pop($state);
        self::assertInstanceOf(ConstraintInterface::class, $constraint);
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\CreateTable::getSqlString
     */
    public function testGetSqlString()
    {
        $ct = new CreateTable('foo');
        self::assertEquals("CREATE TABLE \"foo\" ( \n)", $ct->getSqlString());

        $ct = new CreateTable('foo', true);
        self::assertEquals("CREATE TEMPORARY TABLE \"foo\" ( \n)", $ct->getSqlString());

        $ct = new CreateTable('foo');
        $ct->addColumn(new Column('bar'));
        self::assertEquals("CREATE TABLE \"foo\" ( \n    \"bar\" INTEGER NOT NULL \n)", $ct->getSqlString());

        $ct = new CreateTable('foo', true);
        $ct->addColumn(new Column('bar'));
        self::assertEquals("CREATE TEMPORARY TABLE \"foo\" ( \n    \"bar\" INTEGER NOT NULL \n)", $ct->getSqlString());

        $ct = new CreateTable('foo', true);
        $ct->addColumn(new Column('bar'));
        $ct->addColumn(new Column('baz'));
        self::assertEquals(
            "CREATE TEMPORARY TABLE \"foo\" ( \n    \"bar\" INTEGER NOT NULL,\n    \"baz\" INTEGER NOT NULL \n)",
            $ct->getSqlString()
        );

        $ct = new CreateTable('foo');
        $ct->addColumn(new Column('bar'));
        $ct->addConstraint(new Constraint\PrimaryKey('bat'));
        self::assertEquals(
            "CREATE TABLE \"foo\" ( \n    \"bar\" INTEGER NOT NULL , \n    PRIMARY KEY (\"bat\") \n)",
            $ct->getSqlString()
        );

        $ct = new CreateTable('foo');
        $ct->addConstraint(new Constraint\PrimaryKey('bar'));
        $ct->addConstraint(new Constraint\PrimaryKey('bat'));
        self::assertEquals(
            "CREATE TABLE \"foo\" ( \n    PRIMARY KEY (\"bar\"),\n    PRIMARY KEY (\"bat\") \n)",
            $ct->getSqlString()
        );

        $ct = new CreateTable(new TableIdentifier('foo'));
        $ct->addColumn(new Column('bar'));
        self::assertEquals("CREATE TABLE \"foo\" ( \n    \"bar\" INTEGER NOT NULL \n)", $ct->getSqlString());

        $ct = new CreateTable(new TableIdentifier('bar', 'foo'));
        $ct->addColumn(new Column('baz'));
        self::assertEquals("CREATE TABLE \"foo\".\"bar\" ( \n    \"baz\" INTEGER NOT NULL \n)", $ct->getSqlString());
    }
}
