<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn;
use PHPUnit\Framework\TestCase;

class AbstractLengthColumnTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn::setLength
     */
    public function testSetLength()
    {
        $column = $this->getMockForAbstractClass(AbstractLengthColumn::class, ['foo', 55]);
        self::assertEquals(55, $column->getLength());
        self::assertSame($column, $column->setLength(20));
        self::assertEquals(20, $column->getLength());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn::getLength
     */
    public function testGetLength()
    {
        $column = $this->getMockForAbstractClass(AbstractLengthColumn::class, ['foo', 55]);
        self::assertEquals(55, $column->getLength());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = $this->getMockForAbstractClass(AbstractLengthColumn::class, ['foo', 4]);

        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'INTEGER(4)'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
