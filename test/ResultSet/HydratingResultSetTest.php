<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\ResultSet;

use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\ArraySerializable;
use Laminas\Hydrator\ClassMethods;

class HydratingResultSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\ResultSet\HydratingResultSet::setObjectPrototype
     */
    public function testSetObjectPrototype()
    {
        $prototype = new \stdClass;
        $hydratingRs = new HydratingResultSet;
        $this->assertSame($hydratingRs, $hydratingRs->setObjectPrototype($prototype));
    }

    /**
     * @covers Laminas\Db\ResultSet\HydratingResultSet::getObjectPrototype
     */
    public function testGetObjectPrototype()
    {
        $hydratingRs = new HydratingResultSet;
        $this->assertInstanceOf('ArrayObject', $hydratingRs->getObjectPrototype());
    }

    /**
     * @covers Laminas\Db\ResultSet\HydratingResultSet::setHydrator
     */
    public function testSetHydrator()
    {
        $hydratingRs = new HydratingResultSet;
        $this->assertSame($hydratingRs, $hydratingRs->setHydrator(new ClassMethods()));
    }

    /**
     * @covers Laminas\Db\ResultSet\HydratingResultSet::getHydrator
     */
    public function testGetHydrator()
    {
        $hydratingRs = new HydratingResultSet;
        $this->assertInstanceOf(ArraySerializable::class, $hydratingRs->getHydrator());
    }

    /**
     * @covers Laminas\Db\ResultSet\HydratingResultSet::current
     */
    public function testCurrent()
    {
        $hydratingRs = new HydratingResultSet;
        $hydratingRs->initialize([
            ['id' => 1, 'name' => 'one']
        ]);
        $obj = $hydratingRs->current();
        $this->assertInstanceOf('ArrayObject', $obj);
    }

    /**
     * @covers Laminas\Db\ResultSet\HydratingResultSet::toArray
     * @todo   Implement testToArray().
     */
    public function testToArray()
    {
        $hydratingRs = new HydratingResultSet;
        $hydratingRs->initialize([
            ['id' => 1, 'name' => 'one']
        ]);
        $obj = $hydratingRs->toArray();
        $this->assertInternalType('array', $obj);
    }
}
