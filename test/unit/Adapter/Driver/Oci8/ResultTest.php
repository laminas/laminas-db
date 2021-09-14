<?php

namespace LaminasTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Driver\Oci8\Result;
use PHPUnit\Framework\TestCase;

/**
 * @group result-oci8
 */
class ResultTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Result::getResource
     */
    public function testGetResource()
    {
        $result = new Result();
        self::assertNull($result->getResource());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Result::buffer
     */
    public function testBuffer()
    {
        $result = new Result();
        self::assertNull($result->buffer());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Result::isBuffered
     */
    public function testIsBuffered()
    {
        $result = new Result();
        self::assertFalse($result->isBuffered());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Result::getGeneratedValue
     */
    public function testGetGeneratedValue()
    {
        $result = new Result();
        self::assertNull($result->getGeneratedValue());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Result::key
     */
    public function testKey()
    {
        $result = new Result();
        self::assertEquals(0, $result->key());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Result::next
     */
    public function testNext()
    {
        $mockResult = $this->getMockBuilder(Result::class)
            ->setMethods(['loadData'])
            ->getMock();
        $mockResult->expects($this->any())
            ->method('loadData')
            ->willReturn(null);
        self::assertNull($mockResult->next());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Result::rewind
     */
    public function testRewind()
    {
        $result = new Result();
        self::assertNull($result->rewind());
    }
}
