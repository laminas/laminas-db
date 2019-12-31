<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use PHPUnit\Framework\TestCase;

class AbstractLengthColumnTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn::setLength
     */
    public function testSetLength()
    {
        $column = $this->getMockForAbstractClass('Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn', ['foo', 55]);
        self::assertEquals(55, $column->getLength());
        self::assertSame($column, $column->setLength(20));
        self::assertEquals(20, $column->getLength());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn::getLength
     */
    public function testGetLength()
    {
        $column = $this->getMockForAbstractClass('Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn', ['foo', 55]);
        self::assertEquals(55, $column->getLength());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = $this->getMockForAbstractClass('Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn', ['foo', 4]);

        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'INTEGER(4)'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
