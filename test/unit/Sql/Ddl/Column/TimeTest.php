<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Time;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Time::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Time('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'TIME'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
