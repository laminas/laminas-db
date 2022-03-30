<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Char;
use PHPUnit\Framework\TestCase;

class CharTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Char::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Char('foo', 20);
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'CHAR(20)'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
