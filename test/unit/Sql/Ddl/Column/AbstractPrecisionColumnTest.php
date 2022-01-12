<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn;
use PHPUnit\Framework\TestCase;

class AbstractPrecisionColumnTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn::setDigits
     */
    public function testSetDigits()
    {
        $column = $this->getMockForAbstractClass(AbstractPrecisionColumn::class, ['foo', 10]);
        self::assertEquals(10, $column->getDigits());
        self::assertSame($column, $column->setDigits(12));
        self::assertEquals(12, $column->getDigits());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn::getDigits
     */
    public function testGetDigits()
    {
        $column = $this->getMockForAbstractClass(AbstractPrecisionColumn::class, ['foo', 10]);
        self::assertEquals(10, $column->getDigits());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn::setDecimal
     */
    public function testSetDecimal()
    {
        $column = $this->getMockForAbstractClass(AbstractPrecisionColumn::class, ['foo', 10, 5]);
        self::assertEquals(5, $column->getDecimal());
        self::assertSame($column, $column->setDecimal(2));
        self::assertEquals(2, $column->getDecimal());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn::getDecimal
     */
    public function testGetDecimal()
    {
        $column = $this->getMockForAbstractClass(AbstractPrecisionColumn::class, ['foo', 10, 5]);
        self::assertEquals(5, $column->getDecimal());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = $this->getMockForAbstractClass(AbstractPrecisionColumn::class, ['foo', 10, 5]);

        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'INTEGER(10,5)'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
