<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql;

use Laminas\Db\Sql\Sql;

class TableGatewayTest extends \PHPUnit_Framework_TestCase
{

    protected $mockAdapter = null;

    /**
     * Sql object
     * @var Sql
     */
    protected $sql = null;

    public function setup()
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMock('Laminas\Db\Adapter\Driver\ResultInterface');
        $mockStatement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));
        $mockConnection = $this->getMock('Laminas\Db\Adapter\Driver\ConnectionInterface');
        $mockDriver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        // setup mock adapter
        $this->mockAdapter = $this->getMock('Laminas\Db\Adapter\Adapter', null, array($mockDriver));

        $this->sql = new Sql($this->mockAdapter, 'foo');
    }

    /**
     * @covers Laminas\Db\Sql\Sql::__construct
     */
    public function test__construct()
    {
        $sql = new Sql($this->mockAdapter);

        $this->assertFalse($sql->hasTable());

        $sql->setTable('foo');
        $this->assertSame('foo', $sql->getTable());

        $this->setExpectedException('Laminas\Db\Sql\Exception\InvalidArgumentException', 'Table must be a string, array or instance of TableIdentifier.');
        $sql->setTable(null);
    }

    /**
     * @covers Laminas\Db\Sql\Sql::select
     */
    public function testSelect()
    {
        $select = $this->sql->select();
        $this->assertInstanceOf('Laminas\Db\Sql\Select', $select);
        $this->assertSame('foo', $select->getRawState('table'));

        $this->setExpectedException('Laminas\Db\Sql\Exception\InvalidArgumentException',
            'This Sql object is intended to work with only the table "foo" provided at construction time.');
        $this->sql->select('bar');
    }

    /**
     * @covers Laminas\Db\Sql\Sql::insert
     */
    public function testInsert()
    {
        $insert = $this->sql->insert();
        $this->assertInstanceOf('Laminas\Db\Sql\Insert', $insert);
        $this->assertSame('foo', $insert->getRawState('table'));

        $this->setExpectedException('Laminas\Db\Sql\Exception\InvalidArgumentException',
            'This Sql object is intended to work with only the table "foo" provided at construction time.');
        $this->sql->insert('bar');
    }

    /**
     * @covers Laminas\Db\Sql\Sql::update
     */
    public function testUpdate()
    {
        $update = $this->sql->update();
        $this->assertInstanceOf('Laminas\Db\Sql\Update', $update);
        $this->assertSame('foo', $update->getRawState('table'));

        $this->setExpectedException('Laminas\Db\Sql\Exception\InvalidArgumentException',
            'This Sql object is intended to work with only the table "foo" provided at construction time.');
        $this->sql->update('bar');

    }

    /**
     * @covers Laminas\Db\Sql\Sql::delete
     */
    public function testDelete()
    {
        $delete = $this->sql->delete();


        $this->assertInstanceOf('Laminas\Db\Sql\Delete', $delete);
        $this->assertSame('foo', $delete->getRawState('table'));

        $this->setExpectedException('Laminas\Db\Sql\Exception\InvalidArgumentException',
            'This Sql object is intended to work with only the table "foo" provided at construction time.');
        $this->sql->delete('bar');

    }

    /**
     * @covers Laminas\Db\Sql\Sql::prepareStatementForSqlObject
     */
    public function testPrepareStatementForSqlObject()
    {
        $insert = $this->sql->insert()->columns(array('foo'));
        $stmt = $this->sql->prepareStatementForSqlObject($insert);
        $this->assertInstanceOf('Laminas\Db\Adapter\Driver\StatementInterface', $stmt);
    }
}
