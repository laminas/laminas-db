<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\ResultSet;

use PHPUnit\Framework\TestCase;

class AbstractResultSetTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultSet;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::initialize
     */
    public function testInitialize()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');

        self::assertSame($resultSet, $resultSet->initialize([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));

        $this->expectException('Laminas\Db\ResultSet\Exception\InvalidArgumentException');
        $this->expectExceptionMessage(
            'DataSource provided is not an array, nor does it implement Iterator or IteratorAggregate'
        );
        $resultSet->initialize('foo');
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::initialize
     */
    public function testInitializeDoesNotCallCount()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $result = $this->getMockForAbstractClass('Laminas\Db\Adapter\Driver\ResultInterface');
        $result->expects($this->never())->method('count');
        $resultSet->initialize($result);
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::initialize
     */
    public function testInitializeWithEmptyArray()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        self::assertSame($resultSet, $resultSet->initialize([]));
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::buffer
     */
    public function testBuffer()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        self::assertSame($resultSet, $resultSet->buffer());

        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
                ['id' => 1, 'name' => 'one'],
                ['id' => 2, 'name' => 'two'],
                ['id' => 3, 'name' => 'three'],
        ]));
        $resultSet->next(); // start iterator
        $this->expectException('Laminas\Db\ResultSet\Exception\RuntimeException');
        $this->expectExceptionMessage('Buffering must be enabled before iteration is started');
        $resultSet->buffer();
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::isBuffered
     */
    public function testIsBuffered()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        self::assertFalse($resultSet->isBuffered());
        $resultSet->buffer();
        self::assertTrue($resultSet->isBuffered());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::getDataSource
     */
    public function testGetDataSource()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        self::assertInstanceOf('\ArrayIterator', $resultSet->getDataSource());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::getFieldCount
     */
    public function testGetFieldCount()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
        ]));
        self::assertEquals(2, $resultSet->getFieldCount());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::next
     */
    public function testNext()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        self::assertNull($resultSet->next());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::key
     */
    public function testKey()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        $resultSet->next();
        self::assertEquals(1, $resultSet->key());
        $resultSet->next();
        self::assertEquals(2, $resultSet->key());
        $resultSet->next();
        self::assertEquals(3, $resultSet->key());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::current
     */
    public function testCurrent()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        self::assertEquals(['id' => 1, 'name' => 'one'], $resultSet->current());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::valid
     */
    public function testValid()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        self::assertTrue($resultSet->valid());
        $resultSet->next();
        $resultSet->next();
        $resultSet->next();
        self::assertFalse($resultSet->valid());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::rewind
     */
    public function testRewind()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        self::assertNull($resultSet->rewind());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::count
     */
    public function testCount()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        self::assertEquals(3, $resultSet->count());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::toArray
     */
    public function testToArray()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        self::assertEquals(
            [
                ['id' => 1, 'name' => 'one'],
                ['id' => 2, 'name' => 'two'],
                ['id' => 3, 'name' => 'three'],
            ],
            $resultSet->toArray()
        );
    }

    /**
     * Test multiple iterations with buffer
     * @group issue-6845
     */
    public function testBufferIterations()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        $resultSet->buffer();

        $data = $resultSet->current();
        self::assertEquals(1, $data['id']);
        $resultSet->next();
        $data = $resultSet->current();
        self::assertEquals(2, $data['id']);

        $resultSet->rewind();
        $data = $resultSet->current();
        self::assertEquals(1, $data['id']);
        $resultSet->next();
        $data = $resultSet->current();
        self::assertEquals(2, $data['id']);
        $resultSet->next();
        $data = $resultSet->current();
        self::assertEquals(3, $data['id']);
    }
}
