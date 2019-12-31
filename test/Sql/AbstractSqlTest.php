<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Platform\Sql92;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\ExpressionInterface;
use Laminas\Db\Sql\Predicate;
use Laminas\Db\Sql\Select;

class AbstractSqlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $abstractSql = null;

    public function setup()
    {
        $this->abstractSql = $this->getMockForAbstractClass('Laminas\Db\Sql\AbstractSql');
    }

    /**
     * @covers Laminas\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWithoutDriver()
    {
        $expression = new Expression('? > ? AND y < ?', array('x', 5, 10), array(Expression::TYPE_IDENTIFIER));
        $sqlAndParams = $this->invokeProcessExpressionMethod($expression);

        $this->assertEquals("\"x\" > '5' AND y < '10'", $sqlAndParams->getSql());
        $this->assertInstanceOf('Laminas\Db\Adapter\ParameterContainer', $sqlAndParams->getParameterContainer());
        $this->assertEquals(0, $sqlAndParams->getParameterContainer()->count());
    }

    /**
     * @covers Laminas\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWithDriverAndParameterizationTypeNamed()
    {
        $mockDriver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue(DriverInterface::PARAMETERIZATION_NAMED));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnCallback(function ($x) {
            return ':' . $x;
        }));

        $expression = new Expression('? > ? AND y < ?', array('x', 5, 10), array(Expression::TYPE_IDENTIFIER));
        $sqlAndParams = $this->invokeProcessExpressionMethod($expression, $mockDriver);

        $parameterContainer = $sqlAndParams->getParameterContainer();
        $parameters = $parameterContainer->getNamedArray();

        $this->assertRegExp('#"x" > :expr\d\d\d\dParam1 AND y < :expr\d\d\d\dParam2#', $sqlAndParams->getSql());

        // test keys and values
        preg_match('#expr(\d\d\d\d)Param1#', key($parameters), $matches);
        $expressionNumber = $matches[1];

        $this->assertRegExp('#expr\d\d\d\dParam1#', key($parameters));
        $this->assertEquals(5, current($parameters));
        next($parameters);
        $this->assertRegExp('#expr\d\d\d\dParam2#', key($parameters));
        $this->assertEquals(10, current($parameters));

        // ensure next invocation increases number by 1
        $sqlAndParamsNext = $this->invokeProcessExpressionMethod($expression, $mockDriver);

        $parameterContainer = $sqlAndParamsNext->getParameterContainer();
        $parameters = $parameterContainer->getNamedArray();

        preg_match('#expr(\d\d\d\d)Param1#', key($parameters), $matches);
        $expressionNumberNext = $matches[1];

        $this->assertEquals(1, (int) $expressionNumberNext - (int) $expressionNumber);
    }

    /**
     * @covers Laminas\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWorksWithExpressionContainingStringParts()
    {
        $expression = new Predicate\Expression('x = ?', 5);

        $predicateSet = new Predicate\PredicateSet(array(new Predicate\PredicateSet(array($expression))));
        $sqlAndParams = $this->invokeProcessExpressionMethod($predicateSet);

        $this->assertEquals("(x = '5')", $sqlAndParams->getSql());
        $this->assertEquals(0, $sqlAndParams->getParameterContainer()->count());
    }

    /**
     * @covers Laminas\Db\Sql\AbstractSql::processExpression
     */
    public function testProcessExpressionWorksWithExpressionContainingSelectObject()
    {
        $select = new Select();
        $select->from('x')->where->like('bar', 'Foo%');
        $expression = new Predicate\In('x', $select);

        $predicateSet = new Predicate\PredicateSet(array(new Predicate\PredicateSet(array($expression))));
        $sqlAndParams = $this->invokeProcessExpressionMethod($predicateSet);

        $this->assertEquals('("x" IN (SELECT "x".* FROM "x" WHERE "bar" LIKE \'Foo%\'))', $sqlAndParams->getSql());
        $this->assertEquals(0, $sqlAndParams->getParameterContainer()->count());
    }

    public function testProcessExpressionWorksWithExpressionContainingExpressionObject()
    {
        $expression = new Predicate\Operator(
            'release_date',
            '=',
            new Expression('FROM_UNIXTIME(?)', 100000000)
        );

        $sqlAndParams = $this->invokeProcessExpressionMethod($expression);
        $this->assertEquals('"release_date" = FROM_UNIXTIME(\'100000000\')', $sqlAndParams->getSql());
    }

    /**
     * @param \Laminas\Db\Sql\ExpressionInterface $expression
     * @param \Laminas\Db\Adapter\Adapter|null $adapter
     * @return \Laminas\Db\Adapter\StatementContainer
     */
    protected function invokeProcessExpressionMethod(ExpressionInterface $expression, $driver = null)
    {
        $method = new \ReflectionMethod($this->abstractSql, 'processExpression');
        $method->setAccessible(true);
        return $method->invoke($this->abstractSql, $expression, new Sql92, $driver);
    }

}
