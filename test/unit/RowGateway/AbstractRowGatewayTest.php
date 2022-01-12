<?php

namespace LaminasTest\Db\RowGateway;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ConnectionInterface;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\RowGateway\AbstractRowGateway;
use Laminas\Db\RowGateway\Exception\RuntimeException;
use Laminas\Db\RowGateway\RowGateway;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class AbstractRowGatewayTest extends TestCase
{
    /** @var Adapter&MockObject */
    protected $mockAdapter;

    /** @var RowGateway */
    protected $rowGateway;

    /** @var ResultInterface&MockObject */
    protected $mockResult;

    protected function setUp(): void
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMockBuilder(ResultInterface::class)->getMock();
        $mockResult->expects($this->any())->method('getAffectedRows')->will($this->returnValue(1));
        $this->mockResult = $mockResult;
        $mockStatement    = $this->getMockBuilder(StatementInterface::class)->getMock();
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));
        $mockConnection = $this->getMockBuilder(ConnectionInterface::class)->getMock();
        $mockDriver     = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        // setup mock adapter
        $this->mockAdapter = $this->getMockBuilder(Adapter::class)
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();

        $this->rowGateway = $this->getMockForAbstractClass(AbstractRowGateway::class);

        $rgPropertyValues = [
            'primaryKeyColumn' => 'id',
            'table'            => 'foo',
            'sql'              => new Sql($this->mockAdapter),
        ];
        $this->setRowGatewayState($rgPropertyValues);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::offsetSet
     */
    public function testOffsetSet()
    {
        // If we set with an index, both getters should retrieve the same value:
        $this->rowGateway['testColumn'] = 'test';
        self::assertEquals('test', $this->rowGateway->testColumn);
        self::assertEquals('test', $this->rowGateway['testColumn']);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::__set
     */
    // @codingStandardsIgnoreStart
    public function test__set()
    {
        // @codingStandardsIgnoreEnd
        // If we set with a property, both getters should retrieve the same value:
        $this->rowGateway->testColumn = 'test';
        self::assertEquals('test', $this->rowGateway->testColumn);
        self::assertEquals('test', $this->rowGateway['testColumn']);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::__isset
     */
    // @codingStandardsIgnoreStart
    public function test__isset()
    {
        // @codingStandardsIgnoreEnd
        // Test isset before and after assigning to a property:
        self::assertFalse(isset($this->rowGateway->foo));
        $this->rowGateway->foo = 'bar';
        self::assertTrue(isset($this->rowGateway->foo));
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::offsetExists
     */
    public function testOffsetExists()
    {
        // Test isset before and after assigning to an index:
        self::assertFalse(isset($this->rowGateway['foo']));
        $this->rowGateway['foo'] = 'bar';
        self::assertTrue(isset($this->rowGateway['foo']));
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::__unset
     */
    // @codingStandardsIgnoreStart
    public function test__unset()
    {
        // @codingStandardsIgnoreEnd
        $this->rowGateway->foo = 'bar';
        self::assertEquals('bar', $this->rowGateway->foo);
        unset($this->rowGateway->foo);
        self::assertEmpty($this->rowGateway->foo);
        self::assertEmpty($this->rowGateway['foo']);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::offsetUnset
     */
    public function testOffsetUnset()
    {
        $this->rowGateway['foo'] = 'bar';
        self::assertEquals('bar', $this->rowGateway['foo']);
        unset($this->rowGateway['foo']);
        self::assertEmpty($this->rowGateway->foo);
        self::assertEmpty($this->rowGateway['foo']);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::offsetGet
     */
    public function testOffsetGet()
    {
        // If we set with an index, both getters should retrieve the same value:
        $this->rowGateway['testColumn'] = 'test';
        self::assertEquals('test', $this->rowGateway->testColumn);
        self::assertEquals('test', $this->rowGateway['testColumn']);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::__get
     */
    // @codingStandardsIgnoreStart
    public function test__get()
    {
        // @codingStandardsIgnoreEnd
        // If we set with a property, both getters should retrieve the same value:
        $this->rowGateway->testColumn = 'test';
        self::assertEquals('test', $this->rowGateway->testColumn);
        self::assertEquals('test', $this->rowGateway['testColumn']);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::save
     */
    public function testSaveInsert()
    {
        // test insert
        $this->mockResult->expects($this->any())->method('current')
            ->will($this->returnValue(['id' => 5, 'name' => 'foo']));
        $this->mockResult->expects($this->any())->method('getGeneratedValue')->will($this->returnValue(5));
        $this->rowGateway->populate(['name' => 'foo']);
        $this->rowGateway->save();
        self::assertEquals(5, $this->rowGateway->id);
        self::assertEquals(5, $this->rowGateway['id']);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::save
     */
    public function testSaveInsertMultiKey()
    {
        $this->rowGateway = $this->getMockForAbstractClass(AbstractRowGateway::class);

        $mockSql = $this->getMockForAbstractClass(Sql::class, [$this->mockAdapter]);

        $rgPropertyValues = [
            'primaryKeyColumn' => ['one', 'two'],
            'table'            => 'foo',
            'sql'              => $mockSql,
        ];
        $this->setRowGatewayState($rgPropertyValues);

        // test insert
        $this->mockResult->expects($this->any())->method('current')
            ->will($this->returnValue(['one' => 'foo', 'two' => 'bar']));

        // @todo Need to assert that $where was filled in

        $refRowGateway     = new ReflectionObject($this->rowGateway);
        $refRowGatewayProp = $refRowGateway->getProperty('primaryKeyData');
        $refRowGatewayProp->setAccessible(true);

        $this->rowGateway->populate(['one' => 'foo', 'two' => 'bar']);

        self::assertNull($refRowGatewayProp->getValue($this->rowGateway));

        // save should setup the primaryKeyData
        $this->rowGateway->save();

        self::assertEquals(['one' => 'foo', 'two' => 'bar'], $refRowGatewayProp->getValue($this->rowGateway));
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::save
     */
    public function testSaveUpdate()
    {
        // test update
        $this->mockResult->expects($this->any())->method('current')
            ->will($this->returnValue(['id' => 6, 'name' => 'foo']));
        $this->rowGateway->populate(['id' => 6, 'name' => 'foo'], true);
        $this->rowGateway->save();
        self::assertEquals(6, $this->rowGateway['id']);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::save
     */
    public function testSaveUpdateChangingPrimaryKey()
    {
        // this mock is the select to be used to re-fresh the rowobject's data
        $selectMock = $this->getMockBuilder(Select::class)
            ->setMethods(['where'])
            ->getMock();
        $selectMock->expects($this->once())
            ->method('where')
            ->with($this->equalTo(['id' => 7]))
            ->will($this->returnValue($selectMock));

        $sqlMock = $this->getMockBuilder(Sql::class)
            ->setMethods(['select'])
            ->setConstructorArgs([$this->mockAdapter])
            ->getMock();
        $sqlMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($selectMock));

        $this->setRowGatewayState(['sql' => $sqlMock]);

        // original mock returning updated data
        $this->mockResult->expects($this->any())
            ->method('current')
            ->will($this->returnValue(['id' => 7, 'name' => 'fooUpdated']));

        // populate forces an update in save(), seeds with original data (from db)
        $this->rowGateway->populate(['id' => 6, 'name' => 'foo'], true);
        $this->rowGateway->id = 7;
        $this->rowGateway->save();
        self::assertEquals(['id' => 7, 'name' => 'fooUpdated'], $this->rowGateway->toArray());
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::delete
     */
    public function testDelete()
    {
        $this->rowGateway->foo = 'bar';
        $affectedRows          = $this->rowGateway->delete();
        self::assertFalse($this->rowGateway->rowExistsInDatabase());
        self::assertEquals(1, $affectedRows);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::populate
     * @covers \Laminas\Db\RowGateway\RowGateway::rowExistsInDatabase
     */
    public function testPopulate()
    {
        $this->rowGateway->populate(['id' => 5, 'name' => 'foo']);
        self::assertEquals(5, $this->rowGateway['id']);
        self::assertEquals('foo', $this->rowGateway['name']);
        self::assertFalse($this->rowGateway->rowExistsInDatabase());

        $this->rowGateway->populate(['id' => 5, 'name' => 'foo'], true);
        self::assertTrue($this->rowGateway->rowExistsInDatabase());
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::processPrimaryKeyData
     */
    public function testProcessPrimaryKeyData()
    {
        $this->rowGateway->populate(['id' => 5, 'name' => 'foo'], true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('a known key id was not found');
        $this->rowGateway->populate(['boo' => 5, 'name' => 'foo'], true);
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::count
     */
    public function testCount()
    {
        $this->rowGateway->populate(['id' => 5, 'name' => 'foo'], true);
        self::assertEquals(2, $this->rowGateway->count());
    }

    /**
     * @covers \Laminas\Db\RowGateway\RowGateway::toArray
     */
    public function testToArray()
    {
        $this->rowGateway->populate(['id' => 5, 'name' => 'foo'], true);
        self::assertEquals(['id' => 5, 'name' => 'foo'], $this->rowGateway->toArray());
    }

    protected function setRowGatewayState(array $properties)
    {
        $refRowGateway = new ReflectionObject($this->rowGateway);
        foreach ($properties as $rgPropertyName => $rgPropertyValue) {
            $refRowGatewayProp = $refRowGateway->getProperty($rgPropertyName);
            $refRowGatewayProp->setAccessible(true);
            $refRowGatewayProp->setValue($this->rowGateway, $rgPropertyValue);
        }
    }
}
