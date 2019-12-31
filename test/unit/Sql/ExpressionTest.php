<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql;

use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use PHPUnit\Framework\TestCase;

/**
 * This is a unit testing test case.
 * A unit here is a method, there will be at least one test per method
 *
 * Expression is a value object with no dependencies/collaborators, therefore, no fixure needed
 */
class ExpressionTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Expression::setExpression
     * @return Expression
     */
    public function testSetExpression()
    {
        $expression = new Expression();
        $return = $expression->setExpression('Foo Bar');
        self::assertSame($expression, $return);
        return $return;
    }

    /**
     * @covers \Laminas\Db\Sql\Expression::setExpression
     */
    public function testSetExpressionException()
    {
        $expression = new Expression();
        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Supplied expression must be a string.');
        $expression->setExpression(null);
    }

    /**
     * @covers \Laminas\Db\Sql\Expression::getExpression
     * @depends testSetExpression
     */
    public function testGetExpression(Expression $expression)
    {
        self::assertEquals('Foo Bar', $expression->getExpression());
    }

    /**
     * @covers \Laminas\Db\Sql\Expression::setParameters
     */
    public function testSetParameters()
    {
        $expression = new Expression();
        $return = $expression->setParameters('foo');
        self::assertSame($expression, $return);
        return $return;
    }

    /**
     * @covers \Laminas\Db\Sql\Expression::setParameters
     */
    public function testSetParametersException()
    {
        $expression = new Expression('', 'foo');

        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Expression parameters must be a scalar or array.');
        $expression->setParameters(null);
    }

    /**
     * @covers \Laminas\Db\Sql\Expression::getParameters
     * @depends testSetParameters
     */
    public function testGetParameters(Expression $expression)
    {
        self::assertEquals('foo', $expression->getParameters());
    }

    /**
     * @covers \Laminas\Db\Sql\Expression::setTypes
     */
    public function testSetTypes()
    {
        $expression = new Expression();
        $return = $expression->setTypes([
            Expression::TYPE_IDENTIFIER,
            Expression::TYPE_VALUE,
            Expression::TYPE_LITERAL,
        ]);
        self::assertSame($expression, $return);
        return $expression;
    }

    /**
     * @covers \Laminas\Db\Sql\Expression::getTypes
     * @depends testSetTypes
     */
    public function testGetTypes(Expression $expression)
    {
        self::assertEquals(
            [Expression::TYPE_IDENTIFIER, Expression::TYPE_VALUE, Expression::TYPE_LITERAL],
            $expression->getTypes()
        );
    }

    /**
     * @covers \Laminas\Db\Sql\Expression::getExpressionData
     */
    public function testGetExpressionData()
    {
        $expression = new Expression(
            'X SAME AS ? AND Y = ? BUT LITERALLY ?',
            ['foo', 5, 'FUNC(FF%X)'],
            [Expression::TYPE_IDENTIFIER, Expression::TYPE_VALUE, Expression::TYPE_LITERAL]
        );

        self::assertEquals(
            [[
                'X SAME AS %s AND Y = %s BUT LITERALLY %s',
                ['foo', 5, 'FUNC(FF%X)'],
                [Expression::TYPE_IDENTIFIER, Expression::TYPE_VALUE, Expression::TYPE_LITERAL],
            ]],
            $expression->getExpressionData()
        );
        $expression = new Expression(
            'X SAME AS ? AND Y = ? BUT LITERALLY ?',
            [
                ['foo'        => Expression::TYPE_IDENTIFIER],
                [5            => Expression::TYPE_VALUE],
                ['FUNC(FF%X)' => Expression::TYPE_LITERAL],
            ]
        );

        $expected = [[
            'X SAME AS %s AND Y = %s BUT LITERALLY %s',
            ['foo', 5, 'FUNC(FF%X)'],
            [Expression::TYPE_IDENTIFIER, Expression::TYPE_VALUE, Expression::TYPE_LITERAL],
        ]];

        self::assertEquals($expected, $expression->getExpressionData());
    }

    public function testGetExpressionDataWillEscapePercent()
    {
        $expression = new Expression('X LIKE "foo%"');
        self::assertEquals(
            ['X LIKE "foo%%"'],
            $expression->getExpressionData()
        );
    }

    public function testConstructorWithLiteralZero()
    {
        $expression = new Expression('0');
        self::assertSame('0', $expression->getExpression());
    }

    /**
     * @group 7407
     */
    public function testGetExpressionPreservesPercentageSignInFromUnixtime()
    {
        $expressionString = 'FROM_UNIXTIME(date, "%Y-%m")';
        $expression       = new Expression($expressionString);

        self::assertSame($expressionString, $expression->getExpression());
    }

    public function testNumberOfReplacementsConsidersWhenSameVariableIsUsedManyTimes()
    {
        $expression = new Expression('uf.user_id = :user_id OR uf.friend_id = :user_id', ['user_id' => 1]);

        self::assertSame(
            [
                [
                    'uf.user_id = :user_id OR uf.friend_id = :user_id',
                    [1],
                    ['value'],
                ],
            ],
            $expression->getExpressionData()
        );
    }

    /**
     * @dataProvider falsyExpressionParametersProvider
     *
     * @param mixed $falsyParameter
     */
    public function testConstructorWithFalsyValidParameters($falsyParameter)
    {
        $expression = new Expression('?', $falsyParameter);
        self::assertSame($falsyParameter, $expression->getParameters());
    }

    public function testConstructorWithInvalidParameter()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expression parameters must be a scalar or array.');
        new Expression('?', (object)[]);
    }

    public function falsyExpressionParametersProvider()
    {
        return [
            [''],
            ['0'],
            [0],
            [0.0],
            [false],
            [[]],
        ];
    }

    public function testNumberOfReplacementsForExpressionWithParameters()
    {
        $expression = new Expression(':a + :b', ['a' => 1, 'b' => 2]);

        self::assertSame(
            [
                [
                    ':a + :b',
                    [1, 2],
                    ['value', 'value'],
                ],
            ],
            $expression->getExpressionData()
        );
    }
}
