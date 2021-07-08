<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Double;
use PHPUnit\Framework\TestCase;

class DoubleTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Double::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Double('foo', 10, 5);
        self::assertEquals(
            [[
                '%s %s NOT NULL',
                ['foo', 'DOUBLE(10,5)'],
                [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL],
            ]],
            $column->getExpressionData()
        );
    }
}
