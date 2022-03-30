<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Floating;
use PHPUnit\Framework\TestCase;

class FloatingTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Floating::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Floating('foo', 10, 5);
        self::assertEquals(
            [
                [
                    '%s %s NOT NULL',
                    ['foo', 'FLOAT(10,5)'],
                    [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL],
                ],
            ],
            $column->getExpressionData()
        );
    }
}
