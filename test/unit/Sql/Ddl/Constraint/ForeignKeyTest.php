<?php

namespace LaminasTest\Db\Sql\Ddl\Constraint;

use Laminas\Db\Sql\Ddl\Constraint\ForeignKey;
use PHPUnit\Framework\TestCase;

class ForeignKeyTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::setName
     */
    public function testSetName(): ForeignKey
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam');
        self::assertSame($fk, $fk->setName('xxxx'));
        return $fk;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getName
     * @depends testSetName
     */
    public function testGetName(ForeignKey $fk)
    {
        self::assertEquals('xxxx', $fk->getName());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::setReferenceTable
     */
    public function testSetReferenceTable(): ForeignKey
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam');
        self::assertSame($fk, $fk->setReferenceTable('xxxx'));
        return $fk;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getReferenceTable
     * @depends testSetReferenceTable
     */
    public function testGetReferenceTable(ForeignKey $fk)
    {
        self::assertEquals('xxxx', $fk->getReferenceTable());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::setReferenceColumn
     */
    public function testSetReferenceColumn(): ForeignKey
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam');
        self::assertSame($fk, $fk->setReferenceColumn('xxxx'));
        return $fk;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getReferenceColumn
     * @depends testSetReferenceColumn
     */
    public function testGetReferenceColumn(ForeignKey $fk)
    {
        self::assertEquals(['xxxx'], $fk->getReferenceColumn());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::setOnDeleteRule
     */
    public function testSetOnDeleteRule(): ForeignKey
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam');
        self::assertSame($fk, $fk->setOnDeleteRule('CASCADE'));
        return $fk;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getOnDeleteRule
     * @depends testSetOnDeleteRule
     */
    public function testGetOnDeleteRule(ForeignKey $fk)
    {
        self::assertEquals('CASCADE', $fk->getOnDeleteRule());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::setOnUpdateRule
     */
    public function testSetOnUpdateRule(): ForeignKey
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam');
        self::assertSame($fk, $fk->setOnUpdateRule('CASCADE'));
        return $fk;
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getOnUpdateRule
     * @depends testSetOnUpdateRule
     */
    public function testGetOnUpdateRule(ForeignKey $fk)
    {
        self::assertEquals('CASCADE', $fk->getOnUpdateRule());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\ForeignKey::getExpressionData
     */
    public function testGetExpressionData()
    {
        $fk = new ForeignKey('foo', 'bar', 'baz', 'bam', 'CASCADE', 'SET NULL');
        self::assertEquals(
            [
                [
                    'CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s) ON DELETE %s ON UPDATE %s',
                    ['foo', 'bar', 'baz', 'bam', 'CASCADE', 'SET NULL'],
                    [
                        $fk::TYPE_IDENTIFIER,
                        $fk::TYPE_IDENTIFIER,
                        $fk::TYPE_IDENTIFIER,
                        $fk::TYPE_IDENTIFIER,
                        $fk::TYPE_LITERAL,
                        $fk::TYPE_LITERAL,
                    ],
                ],
            ],
            $fk->getExpressionData()
        );
    }
}
