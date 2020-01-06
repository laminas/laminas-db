<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\NotIn;
use Laminas\Db\Sql\Select;
use PHPUnit\Framework\TestCase;

class NotInTest extends TestCase
{
    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $in = new NotIn();
        $in->setIdentifier('foo.bar')
            ->setValueSet([1, 2, 3]);
        $expected = [[
            '%s NOT IN (%s, %s, %s)',
            ['foo.bar', 1, 2, 3],
            [NotIn::TYPE_IDENTIFIER, NotIn::TYPE_VALUE, NotIn::TYPE_VALUE, NotIn::TYPE_VALUE],
        ]];
        self::assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselect()
    {
        $select = new Select;
        $in = new NotIn('foo', $select);
        $expected = [[
            '%s NOT IN %s',
            ['foo', $select],
            [$in::TYPE_IDENTIFIER, $in::TYPE_VALUE],
        ]];
        self::assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselectAndIdentifier()
    {
        $select = new Select;
        $in = new NotIn('foo', $select);
        $expected = [[
            '%s NOT IN %s',
            ['foo', $select],
            [$in::TYPE_IDENTIFIER, $in::TYPE_VALUE],
        ]];
        self::assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselectAndArrayIdentifier()
    {
        $select = new Select;
        $in = new NotIn(['foo', 'bar'], $select);
        $expected = [[
            '(%s, %s) NOT IN %s',
            ['foo', 'bar', $select],
            [$in::TYPE_IDENTIFIER, $in::TYPE_IDENTIFIER, $in::TYPE_VALUE],
        ]];
        self::assertEquals($expected, $in->getExpressionData());
    }
}
