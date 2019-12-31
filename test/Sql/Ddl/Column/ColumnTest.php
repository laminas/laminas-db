<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Column;

class ColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::setName
     */
    public function testSetName()
    {
        $column = new Column();
        $this->assertSame($column, $column->setName('foo'));
        return $column;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::getName
     * @depends testSetName
     */
    public function testGetName(Column $column)
    {
        $this->assertEquals('foo', $column->getName());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::setNullable
     */
    public function testSetNullable()
    {
        $column = new Column;
        $this->assertSame($column, $column->setNullable(true));
        return $column;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::isNullable
     * @depends testSetNullable
     */
    public function testIsNullable(Column $column)
    {
        $this->assertTrue($column->isNullable());
        $column->setNullable(false);
        $this->assertFalse($column->isNullable());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::setDefault
     */
    public function testSetDefault()
    {
        $column = new Column;
        $this->assertSame($column, $column->setDefault('foo bar'));
        return $column;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::getDefault
     * @depends testSetDefault
     */
    public function testGetDefault(Column $column)
    {
        $this->assertEquals('foo bar', $column->getDefault());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::setOptions
     */
    public function testSetOptions()
    {
        $column = new Column;
        $this->assertSame($column, $column->setOptions(array('autoincrement' => true)));
        return $column;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::setOption
     */
    public function testSetOption()
    {
        $column = new Column;
        $this->assertSame($column, $column->setOption('primary', true));
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::getOptions
     * @depends testSetOptions
     */
    public function testGetOptions(Column $column)
    {
        $this->assertEquals(array('autoincrement' => true), $column->getOptions());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Column::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Column;
        $column->setName('foo');
        $this->assertEquals(
            array(array('%s %s', array('foo', 'INTEGER NOT NULL'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );

        $column->setNullable(true);
        $this->assertEquals(
            array(array('%s %s', array('foo', 'INTEGER'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );

        $column->setDefault('bar');
        $this->assertEquals(
            array(array('%s %s DEFAULT %s', array('foo', 'INTEGER', 'bar'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL, $column::TYPE_VALUE))),
            $column->getExpressionData()
        );
    }
}
