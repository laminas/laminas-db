<?php

namespace LaminasTest\Db\Sql;

use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Select;
use LaminasTest\Db\DeprecatedAssertionsTrait;
use PHPUnit\Framework\TestCase;

class JoinTest extends TestCase
{
    use DeprecatedAssertionsTrait;

    public function testInitialPositionIsZero()
    {
        $join = new Join();

        self::assertAttributeEquals(0, 'position', $join);
    }

    public function testNextIncrementsThePosition()
    {
        $join = new Join();

        $join->next();

        self::assertAttributeEquals(1, 'position', $join);
    }

    public function testRewindResetsPositionToZero()
    {
        $join = new Join();

        $join->next();
        $join->next();
        self::assertAttributeEquals(2, 'position', $join);

        $join->rewind();
        self::assertAttributeEquals(0, 'position', $join);
    }

    public function testKeyReturnsTheCurrentPosition()
    {
        $join = new Join();

        $join->next();
        $join->next();
        $join->next();

        self::assertEquals(3, $join->key());
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

        self::assertEquals($expectedSpecification, $join->current());
    }

    public function testValidReturnsTrueIfTheIteratorIsAtAValidPositionAndFalseIfNot()
    {
        $join = new Join();
        $join->join('baz', 'foo.id = baz.id');

        self::assertTrue($join->valid());

        $join->next();

        self::assertFalse($join->valid());
    }

    /**
     * @testdox unit test: Test join() returns Join object (is chainable)
     * @covers \Laminas\Db\Sql\Join::join
     */
    public function testJoin()
    {
        $join = new Join;
        $return = $join->join('baz', 'foo.fooId = baz.fooId', Join::JOIN_LEFT);
        self::assertSame($join, $return);
    }

    public function testJoinFullOuter()
    {
        $join = new Join;
        $return = $join->join('baz', 'foo.fooId = baz.fooId', Join::JOIN_FULL_OUTER);
        self::assertSame($join, $return);
    }

    public function testJoinWillThrowAnExceptionIfNameIsNoValid()
    {
        $join = new Join();

        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage("join() expects '' as a single element associative array");
        $join->join([], false);
    }

    /**
     * @testdox unit test: Test count() returns correct count
     * @covers \Laminas\Db\Sql\Join::count
     * @covers \Laminas\Db\Sql\Join::join
     */
    public function testCount()
    {
        $join = new Join;
        $join->join('baz', 'foo.fooId = baz.fooId', Join::JOIN_LEFT);
        $join->join('bar', 'foo.fooId = bar.fooId', Join::JOIN_LEFT);

        self::assertEquals(2, $join->count());
        self::assertCount($join->count(), $join->getJoins());
    }

    /**
     * @testdox unit test: Test reset() resets the joins
     * @covers \Laminas\Db\Sql\Join::count
     * @covers \Laminas\Db\Sql\Join::join
     * @covers \Laminas\Db\Sql\Join::reset
     */
    public function testReset()
    {
        $join = new Join;
        $join->join('baz', 'foo.fooId = baz.fooId', Join::JOIN_LEFT);
        $join->join('bar', 'foo.fooId = bar.fooId', Join::JOIN_LEFT);
        $join->reset();

        self::assertEquals(0, $join->count());
    }
}
