<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Constraint;

use Laminas\Db\Sql\Ddl\Constraint\ForeignKey;

class ForeignKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::setName
     */
    public function testSetName()
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam');
        $this->assertSame($fk, $fk->setName('xxxx'));
        return $fk;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getName
     * @depends testSetName
     */
    public function testGetName(ForeignKey $fk)
    {
        $this->assertEquals('xxxx', $fk->getName());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::setReferenceTable
     */
    public function testSetReferenceTable()
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam');
        $this->assertSame($fk, $fk->setReferenceTable('xxxx'));
        return $fk;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getReferenceTable
     * @depends testSetReferenceTable
     */
    public function testGetReferenceTable(ForeignKey $fk)
    {
        $this->assertEquals('xxxx', $fk->getReferenceTable());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::setReferenceColumn
     */
    public function testSetReferenceColumn()
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam');
        $this->assertSame($fk, $fk->setReferenceColumn('xxxx'));
        return $fk;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getReferenceColumn
     * @depends testSetReferenceColumn
     */
    public function testGetReferenceColumn(ForeignKey $fk)
    {
        $this->assertEquals('xxxx', $fk->getReferenceColumn());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::setOnDeleteRule
     */
    public function testSetOnDeleteRule()
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam');
        $this->assertSame($fk, $fk->setOnDeleteRule('CASCADE'));
        return $fk;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getOnDeleteRule
     * @depends testSetOnDeleteRule
     */
    public function testGetOnDeleteRule(ForeignKey $fk)
    {
        $this->assertEquals('CASCADE', $fk->getOnDeleteRule());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::setOnUpdateRule
     */
    public function testSetOnUpdateRule()
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam');
        $this->assertSame($fk, $fk->setOnUpdateRule('CASCADE'));
        return $fk;
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getOnUpdateRule
     * @depends testSetOnUpdateRule
     */
    public function testGetOnUpdateRule(ForeignKey $fk)
    {
        $this->assertEquals('CASCADE', $fk->getOnUpdateRule());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getExpressionData
     */
    public function testGetExpressionData()
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam', 'CASCADE', 'SET NULL');
        $this->assertEquals(
            array(array(
                'CONSTRAINT %1$s FOREIGN KEY (%2$s) REFERENCES %3$s (%4$s) ON DELETE %5$s ON UPDATE %6$s',
                array('foo', 'bar', 'baz', 'bam', 'CASCADE', 'SET NULL'),
                array($fk::TYPE_IDENTIFIER, $fk::TYPE_IDENTIFIER, $fk::TYPE_IDENTIFIER, $fk::TYPE_IDENTIFIER, $fk::TYPE_LITERAL, $fk::TYPE_LITERAL)
            )),
            $fk->getExpressionData()
        );
    }
}
