<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\In;
use Laminas\Db\Sql\Select;
use PHPUnit_Framework_TestCase as TestCase;

class InTest extends TestCase
{

    public function testEmptyConstructorYieldsNullIdentifierAndValueSet()
    {
        $in = new In();
        $this->assertNull($in->getIdentifier());
        $this->assertNull($in->getValueSet());
    }

    public function testCanPassIdentifierAndValueSetToConstructor()
    {
        $in = new In();
        $predicate = new In('foo.bar', array(1, 2));
        $this->assertEquals('foo.bar', $predicate->getIdentifier());
        $this->assertEquals(array(1, 2), $predicate->getValueSet());
    }

    public function testIdentifierIsMutable()
    {
        $in = new In();
        $in->setIdentifier('foo.bar');
        $this->assertEquals('foo.bar', $in->getIdentifier());
    }

    public function testValueSetIsMutable()
    {
        $in = new In();
        $in->setValueSet(array(1, 2));
        $this->assertEquals(array(1, 2), $in->getValueSet());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $in = new In();
        $in->setIdentifier('foo.bar')
            ->setValueSet(array(1, 2, 3));
        $expected = array(array(
            '%s IN (%s, %s, %s)',
            array('foo.bar', 1, 2, 3),
            array(In::TYPE_IDENTIFIER, In::TYPE_VALUE, In::TYPE_VALUE, In::TYPE_VALUE),
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselect()
    {
        $in = new In('foo', $select = new Select);
        $expected = array(array(
            '%s IN %s',
            array('foo', $select),
            array($in::TYPE_IDENTIFIER, $in::TYPE_SELECT)
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }
}
