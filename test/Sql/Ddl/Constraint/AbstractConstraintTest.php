<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Constraint;

use PHPUnit\Framework\TestCase;

class AbstractConstraintTest extends TestCase
{
    /** @var \Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint */
    protected $ac;

    protected function setUp()
    {
        $this->ac = $this->getMockForAbstractClass('Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint');
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
