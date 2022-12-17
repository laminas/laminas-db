<?php

namespace LaminasTest\Db\ResultSet;

use ArrayIterator;
use Laminas\Db\Adapter\Driver\Pdo\Result;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\AbstractResultSet;
use Laminas\Db\ResultSet\Exception\InvalidArgumentException;
use Laminas\Db\ResultSet\Exception\RuntimeException;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function assert;

class AbstractResultSetTest extends TestCase
{
    /** @var MockObject */
    protected $resultSet;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::initialize
     */
    public function testInitialize()
    {
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);

        self::assertSame($resultSet, $resultSet->initialize([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));

        $this->expectException(InvalidArgumentException::class);
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
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $result    = $this->getMockForAbstractClass(ResultInterface::class);
        $result->expects($this->never())->method('count');
        $resultSet->initialize($result);
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::initialize
     */
    public function testInitializeWithEmptyArray()
    {
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        self::assertSame($resultSet, $resultSet->initialize([]));
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::buffer
     */
    public function testBuffer()
    {
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        self::assertSame($resultSet, $resultSet->buffer());

        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        $resultSet->next(); // start iterator
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Buffering must be enabled before iteration is started');
        $resultSet->buffer();
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::isBuffered
     */
    public function testIsBuffered()
    {
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        self::assertFalse($resultSet->isBuffered());
        $resultSet->buffer();
        self::assertTrue($resultSet->isBuffered());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::getDataSource
     */
    public function testGetDataSource()
    {
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]));
        self::assertInstanceOf(ArrayIterator::class, $resultSet->getDataSource());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::getFieldCount
     */
    public function testGetFieldCount()
    {
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
            ['id' => 1, 'name' => 'one'],
        ]));
        self::assertEquals(2, $resultSet->getFieldCount());
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::next
     */
    public function testNext()
    {
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
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
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
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
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
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
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
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
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
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
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
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
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
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
     *
     * @group issue-6845
     */
    public function testBufferIterations()
    {
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $resultSet->initialize(new ArrayIterator([
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

    /**
     * Test multiple iterations with buffer with multiple rewind() calls
     *
     * @group issue-6845
     */
    public function testMultipleRewindBufferIterations()
    {
        $resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
        $result    = new Result();
        $stub      = $this->getMockBuilder(PDOStatement::class)->getMock();
        $data      = new ArrayIterator([
            ['id' => 1, 'name' => 'one'],
            ['id' => 2, 'name' => 'two'],
            ['id' => 3, 'name' => 'three'],
        ]);
        assert($stub instanceof PDOStatement); // to suppress IDE type warnings
        $stub->expects($this->any())
            ->method('fetch')
            ->will($this->returnCallback(function () use ($data) {
                $r = $data->current();
                $data->next();
                return $r;
            }));
        $result->initialize($stub, null);
        $result->rewind();
        $result->rewind();
        $resultSet->initialize($result);
        $resultSet->buffer();
        $resultSet->rewind();
        $resultSet->rewind();

        $data = $resultSet->current();
        self::assertEquals(1, $data['id']);
        $resultSet->next();
        $data = $resultSet->current();
        self::assertEquals(2, $data['id']);

        $resultSet->rewind();
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
