<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\NotIn;
use Laminas\Db\Sql\Select;
use PHPUnit_Framework_TestCase as TestCase;

class NotInTest extends TestCase
{

    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $in = new NotIn();
        $in->setIdentifier('foo.bar')
            ->setValueSet(array(1, 2, 3));
        $expected = array(array(
            '%s NOT IN (%s, %s, %s)',
            array('foo.bar', 1, 2, 3),
            array(NotIn::TYPE_IDENTIFIER, NotIn::TYPE_VALUE, NotIn::TYPE_VALUE, NotIn::TYPE_VALUE),
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselect()
    {
        $in = new NotIn('foo', $select = new Select);
        $expected = array(array(
            '%s NOT IN %s',
            array('foo', $select),
            array($in::TYPE_IDENTIFIER, $in::TYPE_VALUE)
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }
}
