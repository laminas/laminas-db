<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Boolean;

class BooleanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Boolean::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Boolean('foo');
        $this->assertEquals(
            array(array('%s %s NOT NULL', array('foo', 'BOOLEAN'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Boolean
     *
     * @group 6257
     */
    public function testIsAlwaysNotNullable()
    {
        $column = new Boolean('foo', true);

        $this->assertFalse($column->isNullable());

        $column->setNullable(true);

        $this->assertFalse($column->isNullable());
    }
}
