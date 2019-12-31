<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\BigInteger;

class BigIntegerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\BigInteger::__construct
     */
    public function testObjectConstruction()
    {
        $integer = new BigInteger('foo');
        $this->assertEquals('foo', $integer->getName());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new BigInteger('foo');
        $this->assertEquals(
            array(array('%s %s', array('foo', 'BIGINT NOT NULL'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );
    }
}
