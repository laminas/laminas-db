<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\ExpressionInterface;
use Laminas\Db\Sql\Predicate;
use Laminas\Db\Sql\Select;
use LaminasTest\Db\TestAsset\TrustingSql92Platform;
use PHPUnit\Framework\TestCase;

class AbstractSqlTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $abstractSql;

    protected $mockDriver;

    protected function setUp()
    {
        $this->abstractSql = $this->getMockForAbstractClass('Laminas\Db\Sql\AbstractSql');

        $this->mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $this->mockDriver
            ->expects($this->any())
            ->method('getPrepareType')
            ->will($this->returnValue(DriverInterface::PARAMETERIZATION_NAMED));
        $this->mockDriver
            ->expects($this->any())
            ->method('formatParameterName')
            ->will($this->returnCallback(function ($x) {
                return ':' . $x;
            }));
    }

    /**
     * @covers \Laminas\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWithoutParameterContainer()
    {
        $expression = new Expression('? > ? AND y < ?', ['x', 5, 10], [Expression::TYPE_IDENTIFIER]);
        $sqlAndParams = $this->invokeProcessExpressionMethod($expression);

        self::assertEquals("\"x\" > '5' AND y < '10'", $sqlAndParams);
    }

    /**
     * @covers \Laminas\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWithParameterContainerAndParameterizationTypeNamed()
    {
        $parameterContainer = new ParameterContainer;
        $expression = new Expression('? > ? AND y < ?', ['x', 5, 10], [Expression::TYPE_IDENTIFIER]);
        $sqlAndParams = $this->invokeProcessExpressionMethod($expression, $parameterContainer);

        $parameters = $parameterContainer->getNamedArray();

        self::assertRegExp('#"x" > :expr\d\d\d\dParam1 AND y < :expr\d\d\d\dParam2#', $sqlAndParams);

        // test keys and values
        preg_match('#expr(\d\d\d\d)Param1#', key($parameters), $matches);
        $expressionNumber = $matches[1];

        self::assertRegExp('#expr\d\d\d\dParam1#', key($parameters));
        self::assertEquals(5, current($parameters));
        next($parameters);
        self::assertRegExp('#expr\d\d\d\dParam2#', key($parameters));
        self::assertEquals(10, current($parameters));

        // ensure next invocation increases number by 1
        $parameterContainer = new ParameterContainer;
        $sqlAndParamsNext = $this->invokeProcessExpressionMethod($expression, $parameterContainer);

        $parameters = $parameterContainer->getNamedArray();

        preg_match('#expr(\d\d\d\d)Param1#', key($parameters), $matches);
        $expressionNumberNext = $matches[1];

        self::assertEquals(1, (int) $expressionNumberNext - (int) $expressionNumber);
    }

    /**
     * @covers \Laminas\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWorksWithExpressionContainingStringParts()
    {
        $expression = new Predicate\Expression('x = ?', 5);

        $predicateSet = new Predicate\PredicateSet([new Predicate\PredicateSet([$expression])]);
        $sqlAndParams = $this->invokeProcessExpressionMethod($predicateSet);

        self::assertEquals("(x = '5')", $sqlAndParams);
    }

    /**
     * @covers \Laminas\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWorksWithExpressionContainingSelectObject()
    {
        $select = new Select();
        $select->from('x')->where->like('bar', 'Foo%');
        $expression = new Predicate\In('x', $select);

        $predicateSet = new Predicate\PredicateSet([new Predicate\PredicateSet([$expression])]);
        $sqlAndParams = $this->invokeProcessExpressionMethod($predicateSet);

        self::assertEquals('("x" IN (SELECT "x".* FROM "x" WHERE "bar" LIKE \'Foo%\'))', $sqlAndParams);
    }

    public function testProcessExpressionWorksWithExpressionContainingExpressionObject()
    {
        $expression = new Predicate\Operator(
            'release_date',
            '=',
            new Expression('FROM_UNIXTIME(?)', 100000000)
        );

        $sqlAndParams = $this->invokeProcessExpressionMethod($expression);
        self::assertEquals('"release_date" = FROM_UNIXTIME(\'100000000\')', $sqlAndParams);
    }

    /**
     * @group 7407
     */
    public function testProcessExpressionWorksWithExpressionObjectWithPercentageSigns()
    {
        $expressionString = 'FROM_UNIXTIME(date, "%Y-%m")';
        $expression       = new Expression($expressionString);
        $sqlString        = $this->invokeProcessExpressionMethod($expression);

        self::assertSame($expressionString, $sqlString);
    }

    public function testProcessExpressionWorksWithNamedParameterPrefix()
    {
        $parameterContainer = new ParameterContainer();
        $namedParameterPrefix = uniqid();
        $expression = new Expression('FROM_UNIXTIME(?)', [10000000]);
        $this->invokeProcessExpressionMethod($expression, $parameterContainer, $namedParameterPrefix);

        self::assertSame($namedParameterPrefix . '1', key($parameterContainer->getNamedArray()));
    }

    public function testProcessExpressionWorksWithNamedParameterPrefixContainingWhitespace()
    {
        $parameterContainer = new ParameterContainer();
        $namedParameterPrefix = "string\ncontaining white space";
        $expression = new Expression('FROM_UNIXTIME(?)', [10000000]);
        $this->invokeProcessExpressionMethod($expression, $parameterContainer, $namedParameterPrefix);

        self::assertSame('string__containing__white__space1', key($parameterContainer->getNamedArray()));
    }

    /**
     * @param \Laminas\Db\Sql\ExpressionInterface $expression
     * @param \Laminas\Db\Adapter\ParameterContainer $parameterContainer
     * @param string $namedParameterPrefix
     * @return \Laminas\Db\Adapter\StatementContainer|string
     */
    protected function invokeProcessExpressionMethod(
        ExpressionInterface $expression,
        $parameterContainer = null,
        $namedParameterPrefix = null
    ) {
        $method = new \ReflectionMethod($this->abstractSql, 'processExpression');
        $method->setAccessible(true);
        return $method->invoke(
            $this->abstractSql,
            $expression,
            new TrustingSql92Platform,
            $this->mockDriver,
            $parameterContainer,
            $namedParameterPrefix
        );
    }
}
