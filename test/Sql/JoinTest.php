<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql;

use Laminas\Db\Sql\Join;
use PHPUnit_Framework_TestCase as TestCase;

class JoinTest extends TestCase
{
    /**
     * @testdox unit test: Test join() returns Join object (is chainable)
     * @covers Laminas\Db\Sql\Join::join
     */
    public function testJoin()
    {
        $join = new Join;
        $return = $join->join('baz', 'foo.fooId = baz.fooId', Join::JOIN_LEFT);
        $this->assertSame($join, $return);
    }

    /**
     * @testdox unit test: Test count() returns correct count
     * @covers Laminas\Db\Sql\Join::count
     * @covers Laminas\Db\Sql\Join::join
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
     * @covers Laminas\Db\Sql\Join::count
     * @covers Laminas\Db\Sql\Join::join
     * @covers Laminas\Db\Sql\Join::reset
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
