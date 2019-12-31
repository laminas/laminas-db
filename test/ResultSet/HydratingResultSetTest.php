<?php
namespace LaminasTest\Db\ResultSet;

use Laminas\Db\ResultSet\HydratingResultSet;

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
     * @covers Laminas\Db\ResultSet\HydratingResultSet::setHydrator
     */
    public function testSetHydrator()
    {
        $hydratingRs = new HydratingResultSet;
        $this->assertSame($hydratingRs, $hydratingRs->setHydrator(new \Laminas\Stdlib\Hydrator\ClassMethods()));
    }

    /**
     * @covers Laminas\Db\ResultSet\HydratingResultSet::getHydrator
     */
    public function testGetHydrator()
    {
        $hydratingRs = new HydratingResultSet;
        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ArraySerializable', $hydratingRs->getHydrator());
    }

    /**
     * @covers Laminas\Db\ResultSet\HydratingResultSet::current
     */
    public function testCurrent()
    {
        $hydratingRs = new HydratingResultSet;
        $hydratingRs->initialize(array(
            array('id' => 1, 'name' => 'one')
        ));
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
        $hydratingRs->initialize(array(
            array('id' => 1, 'name' => 'one')
        ));
        $obj = $hydratingRs->toArray();
        $this->assertInternalType('array', $obj);
    }
}
