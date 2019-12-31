<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\Between;
use PHPUnit_Framework_TestCase as TestCase;

class BetweenTest extends TestCase
{
    /**
     * @var Between
     */
    protected $between = null;

    public function setUp()
    {
        $this->between = new Between();
    }

    /**
     * @covers Laminas\Db\Sql\Predicate\Between::__construct
     * @covers Laminas\Db\Sql\Predicate\Between::getIdentifier
     * @covers Laminas\Db\Sql\Predicate\Between::getMinValue
     * @covers Laminas\Db\Sql\Predicate\Between::getMaxValue
     */
    public function testConstructorYieldsNullIdentifierMinimumAndMaximumValues()
    {
        $this->assertNull($this->between->getIdentifier());
        $this->assertNull($this->between->getMinValue());
        $this->assertNull($this->between->getMaxValue());
    }

    /**
     * @covers Laminas\Db\Sql\Predicate\Between::__construct
     * @covers Laminas\Db\Sql\Predicate\Between::getIdentifier
     * @covers Laminas\Db\Sql\Predicate\Between::getMinValue
     * @covers Laminas\Db\Sql\Predicate\Between::getMaxValue
     */
    public function testConstructorCanPassIdentifierMinimumAndMaximumValues()
    {
        $between = new Between('foo.bar', 1, 300);
        $this->assertEquals('foo.bar', $between->getIdentifier());
        $this->assertEquals(1, $between->getMinValue());
        $this->assertEquals(300, $between->getMaxValue());
    }

    /**
     * @covers Laminas\Db\Sql\Predicate\Between::getSpecification
     */
    public function testSpecificationHasSaneDefaultValue()
    {
        $this->assertEquals('%1$s BETWEEN %2$s AND %3$s', $this->between->getSpecification());
    }



    /**
     * @covers Laminas\Db\Sql\Predicate\Between::setIdentifier
     * @covers Laminas\Db\Sql\Predicate\Between::getIdentifier
     */
    public function testIdentifierIsMutable()
    {
        $this->between->setIdentifier('foo.bar');
        $this->assertEquals('foo.bar', $this->between->getIdentifier());
    }

    /**
     * @covers Laminas\Db\Sql\Predicate\Between::setMinValue
     * @covers Laminas\Db\Sql\Predicate\Between::getMinValue
     */
    public function testMinValueIsMutable()
    {
        $this->between->setMinValue(10);
        $this->assertEquals(10, $this->between->getMinValue());
    }

    /**
     * @covers Laminas\Db\Sql\Predicate\Between::setMaxValue
     * @covers Laminas\Db\Sql\Predicate\Between::getMaxValue
     */
    public function testMaxValueIsMutable()
    {
        $this->between->setMaxValue(10);
        $this->assertEquals(10, $this->between->getMaxValue());
    }

    /**
     * @covers Laminas\Db\Sql\Predicate\Between::setSpecification
     * @covers Laminas\Db\Sql\Predicate\Between::getSpecification
     */
    public function testSpecificationIsMutable()
    {
        $this->between->setSpecification('%1$s IS INBETWEEN %2$s AND %3$s');
        $this->assertEquals('%1$s IS INBETWEEN %2$s AND %3$s', $this->between->getSpecification());
    }

    /**
     * @covers Laminas\Db\Sql\Predicate\Between::getExpressionData
     */
    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $this->between->setIdentifier('foo.bar')
                      ->setMinValue(10)
                      ->setMaxValue(19);
        $expected = array(array(
            $this->between->getSpecification(),
            array('foo.bar', 10, 19),
            array(Between::TYPE_IDENTIFIER, Between::TYPE_VALUE, Between::TYPE_VALUE),
        ));
        $this->assertEquals($expected, $this->between->getExpressionData());
    }
}
