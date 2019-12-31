<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Driver\Oci8\Result;

/**
 * Class ResultTest
 *
 * @package LaminasTest\Db\Adapter\Driver\Oci8
 * @group result-oci8
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Result::getResource
     */
    public function testGetResource()
    {
        $result = new Result();
        $this->assertNull($result->getResource());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Result::buffer
     */
    public function testBuffer()
    {
        $result = new Result();
        $this->assertNull($result->buffer());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Result::isBuffered
     */
    public function testIsBuffered()
    {
        $result = new Result();
        $this->assertFalse($result->isBuffered());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Result::getGeneratedValue
     */
    public function testGetGeneratedValue()
    {
        $result = new Result();
        $this->assertNull($result->getGeneratedValue());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Result::key
     */
    public function testKey()
    {
        $result = new Result();
        $this->assertEquals(0, $result->key());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Result::next
     */
    public function testNext()
    {
        $mockResult = $this->getMockBuilder('Laminas\Db\Adapter\Driver\Oci8\Result')
            ->setMethods(['loadData'])
            ->getMock();
        $mockResult->expects($this->any())
            ->method('loadData')
            ->will($this->returnValue(true));
        $this->assertTrue($mockResult->next());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Result::rewind
     */
    public function testRewind()
    {
        $result = new Result();
        $this->assertNull($result->rewind());
    }
}
