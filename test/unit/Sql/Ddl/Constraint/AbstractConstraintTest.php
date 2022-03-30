<?php

namespace LaminasTest\Db\Sql\Ddl\Constraint;

use Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint;
use PHPUnit\Framework\TestCase;

class AbstractConstraintTest extends TestCase
{
    /** @var AbstractConstraint */
    protected $ac;

    protected function setUp(): void
    {
        $this->ac = $this->getMockForAbstractClass(AbstractConstraint::class);
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint::setColumns
     */
    public function testSetColumns()
    {
        self::assertSame($this->ac, $this->ac->setColumns(['foo', 'bar']));
        self::assertEquals(['foo', 'bar'], $this->ac->getColumns());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint::addColumn
     */
    public function testAddColumn()
    {
        self::assertSame($this->ac, $this->ac->addColumn('foo'));
        self::assertEquals(['foo'], $this->ac->getColumns());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint::getColumns
     */
    public function testGetColumns()
    {
        $this->ac->setColumns(['foo', 'bar']);
        self::assertEquals(['foo', 'bar'], $this->ac->getColumns());
    }
}
