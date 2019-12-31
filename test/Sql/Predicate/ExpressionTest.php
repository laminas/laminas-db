<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Db\Sql\Predicate\IsNull;
use PHPUnit_Framework_TestCase as TestCase;

class ExpressionTest extends TestCase
{
    public function testEmptyConstructorYieldsEmptyLiteralAndParameter()
    {
        $expression = new Expression();
        $this->assertEquals('', $expression->getExpression());
        $this->assertEmpty($expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassLiteralAndSingleScalarParameterToConstructor()
    {
        $expression = new Expression('foo.bar = ?', 'bar');
        $this->assertEquals('foo.bar = ?', $expression->getExpression());
        $this->assertEquals(array('bar'), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassNoParameterToConstructor()
    {
        $expression = new Expression('foo.bar');
        $this->assertEquals(array(), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassSingleNullParameterToConstructor()
    {
        $expression = new Expression('?', null);
        $this->assertEquals(array(null), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassSingleZeroParameterValueToConstructor()
    {
        $predicate = new Expression('?', 0);
        $this->assertEquals(array(0), $predicate->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassSinglePredicateParameterToConstructor()
    {
        $predicate = new IsNull('foo.baz');
        $expression = new Expression('?', $predicate);
        $this->assertEquals(array($predicate), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassMultiScalarParametersToConstructor()
    {
        $expression = new Expression('? OR ?', 'foo', 'bar');
        $this->assertEquals(array('foo', 'bar'), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassMultiNullParametersToConstructor()
    {
        $expression = new Expression('? OR ?', null, null);
        $this->assertEquals(array(null, null), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassMultiPredicateParametersToConstructor()
    {
        $predicate = new IsNull('foo.baz');
        $expression = new Expression('? OR ?', $predicate, $predicate);
        $this->assertEquals(array($predicate, $predicate), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassArrayOfOneScalarParameterToConstructor()
    {
        $expression = new Expression('?', array('foo'));
        $this->assertEquals(array('foo'), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassArrayOfMultiScalarsParameterToConstructor()
    {
        $expression = new Expression('? OR ?', array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassArrayOfOneNullParameterToConstructor()
    {
        $expression = new Expression('?', array(null));
        $this->assertEquals(array(null), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassArrayOfMultiNullsParameterToConstructor()
    {
        $expression = new Expression('? OR ?', array(null, null));
        $this->assertEquals(array(null, null), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassArrayOfOnePredicateParameterToConstructor()
    {
        $predicate = new IsNull('foo.baz');
        $expression = new Expression('?', array($predicate));
        $this->assertEquals(array($predicate), $expression->getParameters());
    }

    /**
     * @group 6849
     */
    public function testCanPassArrayOfMultiPredicatesParameterToConstructor()
    {
        $predicate = new IsNull('foo.baz');
        $expression = new Expression('? OR ?', array($predicate, $predicate));
        $this->assertEquals(array($predicate, $predicate), $expression->getParameters());
    }

    public function testLiteralIsMutable()
    {
        $expression = new Expression();
        $expression->setExpression('foo.bar = ?');
        $this->assertEquals('foo.bar = ?', $expression->getExpression());
    }

    public function testParameterIsMutable()
    {
        $expression = new Expression();
        $expression->setParameters(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $expression->getParameters());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfLiteralAndParametersAndArrayOfTypes()
    {
        $expression = new Expression();
        $expression->setExpression('foo.bar = ? AND id != ?')
                        ->setParameters(array('foo', 'bar'));
        $expected = array(array(
            'foo.bar = %s AND id != %s',
            array('foo', 'bar'),
            array(Expression::TYPE_VALUE, Expression::TYPE_VALUE),
        ));
        $test = $expression->getExpressionData();
        $this->assertEquals($expected, $test, var_export($test, 1));
    }
}
