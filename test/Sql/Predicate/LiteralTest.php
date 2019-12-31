<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\Literal;

class LiteralTest extends \PHPUnit_Framework_TestCase
{
    public function testSetLiteral()
    {
        $literal = new Literal('bar');
        $this->assertSame($literal, $literal->setLiteral('foo'));
    }

    public function testGetLiteral()
    {
        $literal = new Literal('bar');
        $this->assertEquals('bar', $literal->getLiteral());
    }

    public function testGetExpressionData()
    {
        $literal = new Literal('bar');
        $this->assertEquals(array(array('bar', array(), array())), $literal->getExpressionData());
    }
}
