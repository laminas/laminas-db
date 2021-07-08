<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\SmallInteger;
use PHPUnit\Framework\TestCase;

class SmallIntegerTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\SmallInteger::__construct
     */
    public function testObjectConstruction()
    {
        $integer = new SmallInteger('foo');
        self::assertEquals('foo', $integer->getName());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new SmallInteger('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'SMALLINT'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
