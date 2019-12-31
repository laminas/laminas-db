<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Datetime;

class DatetimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Datetime::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Datetime('foo');
        $this->assertEquals(
            [['%s %s NOT NULL', ['foo', 'DATETIME'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
