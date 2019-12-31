<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl;

use Laminas\Db\Sql\Ddl\Column\Column;
use Laminas\Db\Sql\Ddl\CreateTable;

class CreateTableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test object construction
     * @covers Laminas\Db\Sql\Ddl\CreateTable::__construct
     */
    public function testObjectConstruction()
    {
        $ct = new CreateTable('foo', true);
        $this->assertEquals('foo', $ct->getRawState($ct::TABLE));
        $this->assertTrue($ct->isTemporary());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\CreateTable::setTemporary
     */
    public function testSetTemporary()
    {
        $ct = new CreateTable();
        $this->assertSame($ct, $ct->setTemporary(false));
        $this->assertFalse($ct->isTemporary());
        $ct->setTemporary(true);
        $this->assertTrue($ct->isTemporary());
        $ct->setTemporary('yes');
        $this->assertTrue($ct->isTemporary());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\CreateTable::isTemporary
     */
    public function testIsTemporary()
    {
        $ct = new CreateTable();
        $this->assertFalse($ct->isTemporary());
        $ct->setTemporary(true);
        $this->assertTrue($ct->isTemporary());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\CreateTable::setTable
     */
    public function testSetTable()
    {
        $ct = new CreateTable();
        $this->assertEquals('', $ct->getRawState('table'));
        $ct->setTable('test');
        return $ct;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\CreateTable::getRawState
     * @depends testSetTable
     */
    public function testRawStateViaTable(CreateTable $ct)
    {
        $this->assertEquals('test', $ct->getRawState('table'));
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\CreateTable::addColumn
     */
    public function testAddColumn()
    {
        $column = $this->getMock('Laminas\Db\Sql\Ddl\Column\ColumnInterface');
        $ct = new CreateTable;
        $this->assertSame($ct, $ct->addColumn($column));
        return $ct;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\CreateTable::getRawState
     * @depends testAddColumn
     */
    public function testRawStateViaColumn(CreateTable $ct)
    {
        $state = $ct->getRawState('columns');
        $this->assertInternalType('array', $state);
        $column = array_pop($state);
        $this->assertInstanceOf('Laminas\Db\Sql\Ddl\Column\ColumnInterface', $column);
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\CreateTable::addConstraint
     */
    public function testAddConstraint()
    {
        $constraint = $this->getMock('Laminas\Db\Sql\Ddl\Constraint\ConstraintInterface');
        $ct = new CreateTable;
        $this->assertSame($ct, $ct->addConstraint($constraint));
        return $ct;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\CreateTable::getRawState
     * @depends testAddConstraint
     */
    public function testRawStateViaConstraint(CreateTable $ct)
    {
        $state = $ct->getRawState('constraints');
        $this->assertInternalType('array', $state);
        $constraint = array_pop($state);
        $this->assertInstanceOf('Laminas\Db\Sql\Ddl\Constraint\ConstraintInterface', $constraint);
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\CreateTable::getSqlString
     */
    public function testGetSqlString()
    {
        $ct = new CreateTable('foo');
        $this->assertEquals("CREATE TABLE \"foo\" (\n)", $ct->getSqlString());

        $ct = new CreateTable('foo');
        $ct->addColumn(new Column('bar'));
        $this->assertEquals("CREATE TABLE \"foo\" (\n    \"bar\" INTEGER NOT NULL\n)", $ct->getSqlString());
    }
}
