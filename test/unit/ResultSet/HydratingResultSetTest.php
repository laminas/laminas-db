<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\ResultSet;

use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\ArraySerializable;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use PHPUnit\Framework\TestCase;

class HydratingResultSetTest extends TestCase
{
    /** @var string */
    private $arraySerializableHydratorClass;

    /** @var string */
    private $classMethodsHydratorClass;

    protected function setUp()
    {
        $this->arraySerializableHydratorClass = class_exists(ArraySerializableHydrator::class)
            ? ArraySerializableHydrator::class
            : ArraySerializable::class;

        $this->classMethodsHydratorClass = class_exists(ClassMethodsHydrator::class)
            ? ClassMethodsHydrator::class
            : ClassMethods::class;
    }

    /**
     * @covers \Laminas\Db\ResultSet\HydratingResultSet::setObjectPrototype
     */
    public function testSetObjectPrototype()
    {
        $prototype = new \stdClass;
        $hydratingRs = new HydratingResultSet;
        self::assertSame($hydratingRs, $hydratingRs->setObjectPrototype($prototype));
    }

    /**
     * @covers \Laminas\Db\ResultSet\HydratingResultSet::getObjectPrototype
     */
    public function testGetObjectPrototype()
    {
        $hydratingRs = new HydratingResultSet;
        self::assertInstanceOf('ArrayObject', $hydratingRs->getObjectPrototype());
    }

    /**
     * @covers \Laminas\Db\ResultSet\HydratingResultSet::setHydrator
     */
    public function testSetHydrator()
    {
        $hydratingRs = new HydratingResultSet;
        $hydratorClass = $this->classMethodsHydratorClass;
        self::assertSame($hydratingRs, $hydratingRs->setHydrator(new $hydratorClass));
    }

    /**
     * @covers \Laminas\Db\ResultSet\HydratingResultSet::getHydrator
     */
    public function testGetHydrator()
    {
        $hydratingRs = new HydratingResultSet;
        self::assertInstanceOf($this->arraySerializableHydratorClass, $hydratingRs->getHydrator());
    }

    /**
     * @covers \Laminas\Db\ResultSet\HydratingResultSet::current
     */
    public function testCurrentHasData()
    {
        $hydratingRs = new HydratingResultSet;
        $hydratingRs->initialize([
            ['id' => 1, 'name' => 'one'],
        ]);
        $obj = $hydratingRs->current();
        self::assertInstanceOf('ArrayObject', $obj);
    }

    /**
     * @covers \Laminas\Db\ResultSet\HydratingResultSet::current
     */
    public function testCurrentDoesnotHasData()
    {
        $hydratingRs = new HydratingResultSet;
        $hydratingRs->initialize([]);
        $result = $hydratingRs->current();
        self::assertNull($result);
    }

    /**
     * @covers \Laminas\Db\ResultSet\HydratingResultSet::toArray
     * @todo   Implement testToArray().
     */
    public function testToArray()
    {
        $hydratingRs = new HydratingResultSet;
        $hydratingRs->initialize([
            ['id' => 1, 'name' => 'one'],
        ]);
        $obj = $hydratingRs->toArray();
        self::assertInternalType('array', $obj);
    }
}
