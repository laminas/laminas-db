<?php

namespace LaminasTest\Db\TableGateway;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ConnectionInterface;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Update;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use PHPUnit\Framework\MockObject\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AbstractTableGatewayTest extends TestCase
{
    /** @var Generator */
    protected $mockAdapter;

    /** @var Generator */
    protected $mockSql;

    /** @var AbstractTableGateway */
    protected $table;

    /** @var FeatureSet&MockObject */
    protected $mockFeatureSet;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMockBuilder(ResultInterface::class)->getMock();
        $mockResult->expects($this->any())->method('getAffectedRows')->will($this->returnValue(5));

        $mockStatement = $this->getMockBuilder(StatementInterface::class)->getMock();
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));

        $mockConnection = $this->getMockBuilder(ConnectionInterface::class)->getMock();
        $mockConnection->expects($this->any())->method('getLastGeneratedValue')->will($this->returnValue(10));

        $mockDriver = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        $this->mockAdapter = $this->getMockBuilder(Adapter::class)
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();
        $this->mockSql     = $this->getMockBuilder(\Laminas\Db\Sql\Sql::class)
            ->setMethods(['select', 'insert', 'update', 'delete'])
            ->setConstructorArgs([$this->mockAdapter, 'foo'])
            ->getMock();
        $this->mockSql->expects($this->any())->method('select')->will($this->returnValue(
            $this->getMockBuilder(Select::class)
                ->setMethods(['where', 'getRawState'])
                ->setConstructorArgs(['foo'])
                ->getMock()
        ));
        $this->mockSql->expects($this->any())->method('insert')->will($this->returnValue(
            $this->getMockBuilder(Insert::class)
                ->setMethods(['prepareStatement', 'values'])
                ->setConstructorArgs(['foo'])
                ->getMock()
        ));
        $this->mockSql->expects($this->any())->method('update')->will($this->returnValue(
            $this->getMockBuilder(Update::class)
                ->setMethods(['where', 'join'])
                ->setConstructorArgs(['foo'])
                ->getMock()
        ));
        $this->mockSql->expects($this->any())->method('delete')->will($this->returnValue(
            $this->getMockBuilder(Delete::class)
                ->setMethods(['where'])
                ->setConstructorArgs(['foo'])
                ->getMock()
        ));

        $this->mockFeatureSet = $this->getMockBuilder(FeatureSet::class)->getMock();

        $this->table = $this->getMockForAbstractClass(
            AbstractTableGateway::class
            //array('getTable')
        );
        $tgReflection = new ReflectionClass(AbstractTableGateway::class);
        foreach ($tgReflection->getProperties() as $tgPropReflection) {
            $tgPropReflection->setAccessible(true);
            switch ($tgPropReflection->getName()) {
                case 'table':
                    $tgPropReflection->setValue($this->table, 'foo');
                    break;
                case 'adapter':
                    $tgPropReflection->setValue($this->table, $this->mockAdapter);
                    break;
                case 'resultSetPrototype':
                    $tgPropReflection->setValue($this->table, new ResultSet());
                    break;
                case 'sql':
                    $tgPropReflection->setValue($this->table, $this->mockSql);
                    break;
                case 'featureSet':
                    $tgPropReflection->setValue($this->table, $this->mockFeatureSet);
                    break;
            }
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::getTable
     */
    public function testGetTable()
    {
        self::assertEquals('foo', $this->table->getTable());
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::getAdapter
     */
    public function testGetAdapter()
    {
        self::assertSame($this->mockAdapter, $this->table->getAdapter());
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::getSql
     */
    public function testGetSql()
    {
        self::assertInstanceOf(\Laminas\Db\Sql\Sql::class, $this->table->getSql());
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::getResultSetPrototype
     */
    public function testGetSelectResultPrototype()
    {
        self::assertInstanceOf(ResultSet::class, $this->table->getResultSetPrototype());
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::select
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::selectWith
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::executeSelect
     */
    public function testSelectWithNoWhere()
    {
        $resultSet = $this->table->select();

        // check return types
        self::assertInstanceOf(ResultSet::class, $resultSet);
        self::assertNotSame($this->table->getResultSetPrototype(), $resultSet);
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::select
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::selectWith
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::executeSelect
     */
    public function testSelectWithWhereString()
    {
        $mockSelect = $this->mockSql->select();

        $mockSelect->expects($this->any())
            ->method('getRawState')
            ->will($this->returnValue([
                'table'   => $this->table->getTable(),
                'columns' => [],
            ]));

        // assert select::from() is called
        $mockSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo('foo'));

        $this->table->select('foo');
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::select
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::selectWith
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::executeSelect
     *
     * This is a test for the case when a valid $select is built using an aliased table name, then used
     * with AbstractTableGateway::selectWith (or AbstractTableGateway::select).
     *
     * $myTable = new MyTable(...);
     * $sql = new \Laminas\Db\Sql\Sql(...);
     * $select = $sql->select()->from(array('t' => 'mytable'));
     *
     * // Following fails, with Fatal error: Uncaught exception 'RuntimeException' with message
     * 'The table name of the provided select object must match that of the table' unless fix is provided.
     * $myTable->selectWith($select);
     */
    public function testSelectWithArrayTable()
    {
        // Case 1

        $select1 = $this->getMockBuilder(Select::class)->setMethods(['getRawState'])->getMock();
        $select1->expects($this->once())
            ->method('getRawState')
            ->will($this->returnValue([
                'table'   => 'foo', // Standard table name format, valid according to Select::from()
                'columns' => null,
            ]));
        $return = $this->table->selectWith($select1);
        self::assertNotNull($return);

        // Case 2

        $select1 = $this->getMockBuilder(Select::class)->setMethods(['getRawState'])->getMock();
        $select1->expects($this->once())
            ->method('getRawState')
            ->will($this->returnValue([
                'table'   => ['f' => 'foo'], // Alias table name format, valid according to Select::from()
                'columns' => null,
            ]));
        $return = $this->table->selectWith($select1);
        self::assertNotNull($return);
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::insert
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::insertWith
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::executeInsert
     */
    public function testInsert()
    {
        $mockInsert = $this->mockSql->insert();

        $mockInsert->expects($this->once())
            ->method('prepareStatement')
            ->with($this->mockAdapter);

        $mockInsert->expects($this->once())
            ->method('values')
            ->with($this->equalTo(['foo' => 'bar']));

        $affectedRows = $this->table->insert(['foo' => 'bar']);
        self::assertEquals(5, $affectedRows);
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::update
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::updateWith
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::executeUpdate
     */
    public function testUpdate()
    {
        $mockUpdate = $this->mockSql->update();

        // assert select::from() is called
        $mockUpdate->expects($this->once())
            ->method('where')
            ->with($this->equalTo('id = 2'));

        $affectedRows = $this->table->update(['foo' => 'bar'], 'id = 2');
        self::assertEquals(5, $affectedRows);
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::update
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::updateWith
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::executeUpdate
     */
    public function testUpdateWithJoin()
    {
        $mockUpdate = $this->mockSql->update();

        $joins = [
            [
                'name' => 'baz',
                'on'   => 'foo.fooId = baz.fooId',
                'type' => Sql\Join::JOIN_LEFT,
            ],
        ];

        // assert select::from() is called
        $mockUpdate->expects($this->once())
            ->method('where')
            ->with($this->equalTo('id = 2'));

        $mockUpdate->expects($this->once())
            ->method('join')
            ->with($joins[0]['name'], $joins[0]['on'], $joins[0]['type']);

        $affectedRows = $this->table->update(['foo.field' => 'bar'], 'id = 2', $joins);
        self::assertEquals(5, $affectedRows);
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::update
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::updateWith
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::executeUpdate
     */
    public function testUpdateWithJoinDefaultType()
    {
        $mockUpdate = $this->mockSql->update();

        $joins = [
            [
                'name' => 'baz',
                'on'   => 'foo.fooId = baz.fooId',
            ],
        ];

        // assert select::from() is called
        $mockUpdate->expects($this->once())
            ->method('where')
            ->with($this->equalTo('id = 2'));

        $mockUpdate->expects($this->once())
            ->method('join')
            ->with($joins[0]['name'], $joins[0]['on'], Sql\Join::JOIN_INNER);

        $affectedRows = $this->table->update(['foo.field' => 'bar'], 'id = 2', $joins);
        self::assertEquals(5, $affectedRows);
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::update
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::updateWith
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::executeUpdate
     */
    public function testUpdateWithNoCriteria()
    {
        $mockUpdate = $this->mockSql->update();

        $affectedRows = $this->table->update(['foo' => 'bar']);
        self::assertEquals(5, $affectedRows);
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::delete
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::deleteWith
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::executeDelete
     */
    public function testDelete()
    {
        $mockDelete = $this->mockSql->delete();

        // assert select::from() is called
        $mockDelete->expects($this->once())
            ->method('where')
            ->with($this->equalTo('foo'));

        $affectedRows = $this->table->delete('foo');
        self::assertEquals(5, $affectedRows);
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::getLastInsertValue
     */
    public function testGetLastInsertValue()
    {
        $this->table->insert(['foo' => 'bar']);
        self::assertEquals(10, $this->table->getLastInsertValue());
    }

    public function testInitializeBuildsAResultSet()
    {
        $stub = $this->getMockForAbstractClass(AbstractTableGateway::class);

        $tgReflection = new ReflectionClass(AbstractTableGateway::class);
        foreach ($tgReflection->getProperties() as $tgPropReflection) {
            $tgPropReflection->setAccessible(true);
            switch ($tgPropReflection->getName()) {
                case 'table':
                    $tgPropReflection->setValue($stub, 'foo');
                    break;
                case 'adapter':
                    $tgPropReflection->setValue($stub, $this->mockAdapter);
                    break;
                case 'featureSet':
                    $tgPropReflection->setValue($stub, $this->mockFeatureSet);
                    break;
            }
        }

        $stub->initialize();
        $this->assertInstanceOf(ResultSet::class, $stub->getResultSetPrototype());
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::__get
     */
    // @codingStandardsIgnoreStart
    public function test__get()
    {
        // @codingStandardsIgnoreEnd
        $this->table->insert(['foo']); // trigger last insert id update

        self::assertEquals(10, $this->table->lastInsertValue);
        self::assertSame($this->mockAdapter, $this->table->adapter);
        //self::assertEquals('foo', $this->table->table);
    }

    /**
     * @covers \Laminas\Db\TableGateway\AbstractTableGateway::__clone
     */
    // @codingStandardsIgnoreStart
    public function test__clone()
    {
        // @codingStandardsIgnoreEnd
        $cTable = clone $this->table;
        self::assertSame($this->mockAdapter, $cTable->getAdapter());
    }
}
