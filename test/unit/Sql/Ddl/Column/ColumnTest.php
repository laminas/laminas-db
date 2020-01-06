<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Column;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::setName
     */
    public function testSetName()
    {
        $column = new Column();
        self::assertSame($column, $column->setName('foo'));
        return $column;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::getName
     * @depends testSetName
     */
    public function testGetName(Column $column)
    {
        self::assertEquals('foo', $column->getName());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::setNullable
     */
    public function testSetNullable()
    {
        $column = new Column;
        self::assertSame($column, $column->setNullable(true));
        return $column;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::isNullable
     * @depends testSetNullable
     */
    public function testIsNullable(Column $column)
    {
        self::assertTrue($column->isNullable());
        $column->setNullable(false);
        self::assertFalse($column->isNullable());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::setDefault
     */
    public function testSetDefault()
    {
        $column = new Column;
        self::assertSame($column, $column->setDefault('foo bar'));
        return $column;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::getDefault
     * @depends testSetDefault
     */
    public function testGetDefault(Column $column)
    {
        self::assertEquals('foo bar', $column->getDefault());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::setOptions
     */
    public function testSetOptions()
    {
        $column = new Column;
        self::assertSame($column, $column->setOptions(['autoincrement' => true]));
        return $column;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::setOption
     */
    public function testSetOption()
    {
        $column = new Column;
        self::assertSame($column, $column->setOption('primary', true));
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::getOptions
     * @depends testSetOptions
     */
    public function testGetOptions(Column $column)
    {
        self::assertEquals(['autoincrement' => true], $column->getOptions());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Column;
        $column->setName('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'INTEGER'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );

        $column->setNullable(true);
        self::assertEquals(
            [['%s %s', ['foo', 'INTEGER'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );

        $column->setDefault('bar');
        self::assertEquals(
            [[
                '%s %s DEFAULT %s',
                ['foo', 'INTEGER', 'bar'],
                [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL, $column::TYPE_VALUE],
            ]],
            $column->getExpressionData()
        );
    }
}
