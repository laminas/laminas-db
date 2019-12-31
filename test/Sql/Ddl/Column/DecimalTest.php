<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Decimal;

class DecimalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Decimal::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Decimal('foo', 5, 10);
        $this->assertEquals(
            array(array('%s DECIMAL(%s) %s %s', array('foo', '5, 10', 'NOT NULL', ''), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL, $column::TYPE_LITERAL, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );
    }
}
