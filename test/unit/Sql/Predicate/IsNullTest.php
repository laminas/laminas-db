<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\IsNotNull;
use PHPUnit\Framework\TestCase;

class IsNullTest extends TestCase
{
    public function testEmptyConstructorYieldsNullIdentifier()
    {
        $isNotNull = new IsNotNull();
        self::assertNull($isNotNull->getIdentifier());
    }

    public function testSpecificationHasSaneDefaultValue()
    {
        $isNotNull = new IsNotNull();
        self::assertEquals('%1$s IS NOT NULL', $isNotNull->getSpecification());
    }

    public function testCanPassIdentifierToConstructor()
    {
        $isNotNull = new IsNotNull();
        $isnull = new IsNotNull('foo.bar');
        self::assertEquals('foo.bar', $isnull->getIdentifier());
    }

    public function testIdentifierIsMutable()
    {
        $isNotNull = new IsNotNull();
        $isNotNull->setIdentifier('foo.bar');
        self::assertEquals('foo.bar', $isNotNull->getIdentifier());
    }

    public function testSpecificationIsMutable()
    {
        $isNotNull = new IsNotNull();
        $isNotNull->setSpecification('%1$s NOT NULL');
        self::assertEquals('%1$s NOT NULL', $isNotNull->getSpecification());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndArrayOfTypes()
    {
        $isNotNull = new IsNotNull();
        $isNotNull->setIdentifier('foo.bar');
        $expected = [[
            $isNotNull->getSpecification(),
            ['foo.bar'],
            [IsNotNull::TYPE_IDENTIFIER],
        ]];
        self::assertEquals($expected, $isNotNull->getExpressionData());
    }
}
