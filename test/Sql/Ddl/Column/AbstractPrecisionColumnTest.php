<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

class AbstractPrecisionColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn::setDigits
     */
    public function testSetDigits()
    {
        $column = $this->getMockForAbstractClass('Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn', array(
            'foo', 10
        ));
        $this->assertEquals(10, $column->getDigits());
        $this->assertSame($column, $column->setDigits(12));
        $this->assertEquals(12, $column->getDigits());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn::getDigits
     */
    public function testGetDigits()
    {
        $column = $this->getMockForAbstractClass('Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn', array(
            'foo', 10
        ));
        $this->assertEquals(10, $column->getDigits());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn::setDecimal
     */
    public function testSetDecimal()
    {
        $column = $this->getMockForAbstractClass('Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn', array(
            'foo', 10, 5
        ));
        $this->assertEquals(5, $column->getDecimal());
        $this->assertSame($column, $column->setDecimal(2));
        $this->assertEquals(2, $column->getDecimal());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn::getDecimal
     */
    public function testGetDecimal()
    {
        $column = $this->getMockForAbstractClass('Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn', array(
            'foo', 10, 5
        ));
        $this->assertEquals(5, $column->getDecimal());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = $this->getMockForAbstractClass('Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn', array(
            'foo', 10, 5
        ));

        $this->assertEquals(
            array(array('%s %s NOT NULL', array('foo', 'INTEGER(10,5)'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );
    }
}
