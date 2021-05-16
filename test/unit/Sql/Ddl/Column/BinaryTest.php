<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Binary;
use PHPUnit\Framework\TestCase;

class BinaryTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Binary::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Binary('foo', 10000000);
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'BINARY(10000000)'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
