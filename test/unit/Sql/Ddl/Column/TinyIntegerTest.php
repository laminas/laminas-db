<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\TinyInteger;
use PHPUnit\Framework\TestCase;

class TinyIntegerTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\SmallInteger::__construct
     */
    public function testObjectConstruction()
    {
        $integer = new TinyInteger('foo');
        self::assertEquals('foo', $integer->getName());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new TinyInteger('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'TINYINT'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
