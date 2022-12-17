<?php

namespace LaminasTest\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Driver\Pdo\Result;
use Laminas\Db\Adapter\Exception\InvalidArgumentException;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use stdClass;

use function assert;
use function uniqid;

/**
 * @group result-pdo
 */
class ResultTest extends TestCase
{
    /**
     * Tests current method returns same data on consecutive calls.
     *
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Result::current
     */
    public function testCurrent()
    {
        $stub = $this->getMockBuilder('PDOStatement')->getMock();
        $stub->expects($this->any())
            ->method('fetch')
            ->will($this->returnCallback(function () {
                return uniqid();
            }));

        $result = new Result();
        $result->initialize($stub, null);

        self::assertEquals($result->current(), $result->current());
    }

    public function testFetchModeException()
    {
        $result = new Result();

        $this->expectException(InvalidArgumentException::class);
        $result->setFetchMode(13);
    }

    /**
     * Tests whether the fetch mode was set properly and
     */
    public function testFetchModeAnonymousObject()
    {
        $stub = $this->getMockBuilder('PDOStatement')->getMock();
        $stub->expects($this->any())
            ->method('fetch')
            ->will($this->returnCallback(function () {
                return new stdClass();
            }));

        $result = new Result();
        $result->initialize($stub, null);
        $result->setFetchMode(PDO::FETCH_OBJ);

        self::assertEquals(5, $result->getFetchMode());
        self::assertInstanceOf('stdClass', $result->current());
    }

    /**
     * Tests whether the fetch mode has a broader range
     */
    public function testFetchModeRange()
    {
        $stub = $this->getMockBuilder('PDOStatement')->getMock();
        $stub->expects($this->any())
            ->method('fetch')
            ->will($this->returnCallback(function () {
                return new stdClass();
            }));
        $result = new Result();
        $result->initialize($stub, null);
        $result->setFetchMode(PDO::FETCH_NAMED);
        self::assertEquals(11, $result->getFetchMode());
        self::assertInstanceOf('stdClass', $result->current());
    }

    public function testMultipleRewind()
    {
        $data     = [
            ['test' => 1],
            ['test' => 2],
        ];
        $position = 0;

        $stub = $this->getMockBuilder('PDOStatement')->getMock();
        assert($stub instanceof PDOStatement); // to suppress IDE type warnings
        $stub->expects($this->any())
            ->method('fetch')
            ->will($this->returnCallback(function () use ($data, &$position) {
                return $data[$position++];
            }));
        $result = new Result();
        $result->initialize($stub, null);

        $result->rewind();
        $result->rewind();

        $this->assertEquals(0, $result->key());
        $this->assertEquals(1, $position);
        $this->assertEquals($data[0], $result->current());

        $result->next();
        $this->assertEquals(1, $result->key());
        $this->assertEquals(2, $position);
        $this->assertEquals($data[1], $result->current());
    }
}
