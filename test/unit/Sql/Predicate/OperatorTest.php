<?php

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\Operator;
use PHPUnit\Framework\TestCase;

use function var_export;

class OperatorTest extends TestCase
{
    public function testEmptyConstructorYieldsNullLeftAndRightValues()
    {
        $operator = new Operator();
        self::assertNull($operator->getLeft());
        self::assertNull($operator->getRight());
    }

    public function testEmptyConstructorYieldsDefaultsForOperatorAndLeftAndRightTypes()
    {
        $operator = new Operator();
        self::assertEquals(Operator::OP_EQ, $operator->getOperator());
        self::assertEquals(Operator::TYPE_IDENTIFIER, $operator->getLeftType());
        self::assertEquals(Operator::TYPE_VALUE, $operator->getRightType());
    }

    public function testCanPassAllValuesToConstructor()
    {
        $operator = new Operator('bar', '>=', 'foo.bar', Operator::TYPE_VALUE, Operator::TYPE_IDENTIFIER);
        self::assertEquals(Operator::OP_GTE, $operator->getOperator());
        self::assertEquals('bar', $operator->getLeft());
        self::assertEquals('foo.bar', $operator->getRight());
        self::assertEquals(Operator::TYPE_VALUE, $operator->getLeftType());
        self::assertEquals(Operator::TYPE_IDENTIFIER, $operator->getRightType());

        $operator = new Operator(['bar' => Operator::TYPE_VALUE], '>=', ['foo.bar' => Operator::TYPE_IDENTIFIER]);
        self::assertEquals(Operator::OP_GTE, $operator->getOperator());
        self::assertEquals(['bar' => Operator::TYPE_VALUE], $operator->getLeft());
        self::assertEquals(['foo.bar' => Operator::TYPE_IDENTIFIER], $operator->getRight());
        self::assertEquals(Operator::TYPE_VALUE, $operator->getLeftType());
        self::assertEquals(Operator::TYPE_IDENTIFIER, $operator->getRightType());

        $operator = new Operator('bar', '>=', 0);
        self::assertEquals(0, $operator->getRight());
    }

    public function testLeftIsMutable()
    {
        $operator = new Operator();
        $operator->setLeft('foo.bar');
        self::assertEquals('foo.bar', $operator->getLeft());
    }

    public function testRightIsMutable()
    {
        $operator = new Operator();
        $operator->setRight('bar');
        self::assertEquals('bar', $operator->getRight());
    }

    public function testLeftTypeIsMutable()
    {
        $operator = new Operator();
        $operator->setLeftType(Operator::TYPE_VALUE);
        self::assertEquals(Operator::TYPE_VALUE, $operator->getLeftType());
    }

    public function testRightTypeIsMutable()
    {
        $operator = new Operator();
        $operator->setRightType(Operator::TYPE_IDENTIFIER);
        self::assertEquals(Operator::TYPE_IDENTIFIER, $operator->getRightType());
    }

    public function testOperatorIsMutable()
    {
        $operator = new Operator();
        $operator->setOperator(Operator::OP_LTE);
        self::assertEquals(Operator::OP_LTE, $operator->getOperator());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfLeftAndRightAndArrayOfTypes()
    {
        $operator = new Operator();
        $operator->setLeft('foo')
            ->setOperator('>=')
            ->setRight('foo.bar')
            ->setLeftType(Operator::TYPE_VALUE)
            ->setRightType(Operator::TYPE_IDENTIFIER);
        $expected = [
            [
                '%s >= %s',
                ['foo', 'foo.bar'],
                [Operator::TYPE_VALUE, Operator::TYPE_IDENTIFIER],
            ],
        ];
        $test     = $operator->getExpressionData();
        self::assertEquals($expected, $test, var_export($test, 1));
    }
}
