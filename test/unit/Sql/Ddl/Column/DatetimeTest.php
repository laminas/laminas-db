<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Datetime;
use PHPUnit\Framework\TestCase;

class DatetimeTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Datetime::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Datetime('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'DATETIME'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
