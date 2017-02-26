<?php
/**
 * @link      http://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Select;

class JoinTest extends TestCase
{
    public function testInitialPositionIsZero()
    {
        $join = new Join();

        $this->assertAttributeEquals(0, 'position', $join);
    }

    public function testNextIncrementsThePosition()
    {
        $join = new Join();

        $join->next();

        $this->assertAttributeEquals(1, 'position', $join);
    }

    public function testRewindResetsPositionToZero()
    {
        $join = new Join();

        $join->next();
        $join->next();
        $this->assertAttributeEquals(2, 'position', $join);

        $join->rewind();
        $this->assertAttributeEquals(0, 'position', $join);
    }

    public function testKeyReturnsTheCurrentPosition()
    {
        $join = new Join();

        $join->next();
        $join->next();
        $join->next();

        $this->assertEquals(3, $join->key());
    }

    public function testCurrentReturnsTheCurrentJoinSpecification()
    {
        $name = 'baz';
        $on   = 'foo.id = baz.id';

        $join = new Join();
        $join->join($name, $on);

        $expectedSpecification = [
            'name'    => $name,
            'on'      => $on,
            'columns' => [Select::SQL_STAR],
            'type'    => Join::JOIN_INNER,
        ];

        $this->assertEquals($expectedSpecification, $join->current());
    }

    public function testValidReturnsTrueIfTheIteratorIsAtAValidPositionAndFalseIfNot()
    {
        $join = new Join();
        $join->join('baz', 'foo.id = baz.id');

        $this->assertTrue($join->valid());

        $join->next();

        $this->assertFalse($join->valid());
    }

    /**
     * @testdox unit test: Test join() returns Join object (is chainable)
     * @covers Zend\Db\Sql\Join::join
     */
    public function testJoin()
    {
        $join = new Join;
        $return = $join->join('baz', 'foo.fooId = baz.fooId', Join::JOIN_LEFT);
        $this->assertSame($join, $return);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage join() expects '' as a single element associative array
     */
    public function testJoinWillThrowAnExceptionIfNameIsNoValid()
    {
        $join = new Join();
        $join->join([], false);
    }

    /**
     * @testdox unit test: Test count() returns correct count
     * @covers Zend\Db\Sql\Join::count
     * @covers Zend\Db\Sql\Join::join
     */
    public function testCount()
    {
        $join = new Join;
        $join->join('baz', 'foo.fooId = baz.fooId', Join::JOIN_LEFT);
        $join->join('bar', 'foo.fooId = bar.fooId', Join::JOIN_LEFT);

        $this->assertEquals(2, $join->count());
        $this->assertEquals(count($join->getJoins()), $join->count());
    }

    /**
     * @testdox unit test: Test reset() resets the joins
     * @covers Zend\Db\Sql\Join::count
     * @covers Zend\Db\Sql\Join::join
     * @covers Zend\Db\Sql\Join::reset
     */
    public function testReset()
    {
        $join = new Join;
        $join->join('baz', 'foo.fooId = baz.fooId', Join::JOIN_LEFT);
        $join->join('bar', 'foo.fooId = bar.fooId', Join::JOIN_LEFT);
        $join->reset();

        $this->assertEquals(0, $join->count());
    }
}
