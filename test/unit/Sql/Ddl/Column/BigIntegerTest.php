<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\BigInteger;
use PHPUnit\Framework\TestCase;

class BigIntegerTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\BigInteger::__construct
     */
    public function testObjectConstruction()
    {
        $integer = new BigInteger('foo');
        self::assertEquals('foo', $integer->getName());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new BigInteger('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'BIGINT'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
