<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Constraint;

class AbstractConstraintTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint */
    protected $ac;

    public function setup()
    {
        $this->ac = $this->getMockForAbstractClass('Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint');
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint::setColumns
     */
    public function testSetColumns()
    {
        $this->assertSame($this->ac, $this->ac->setColumns(['foo', 'bar']));
        $this->assertEquals(['foo', 'bar'], $this->ac->getColumns());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint::addColumn
     */
    public function testAddColumn()
    {
        $this->assertSame($this->ac, $this->ac->addColumn('foo'));
        $this->assertEquals(['foo'], $this->ac->getColumns());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint::getColumns
     */
    public function testGetColumns()
    {
        $this->ac->setColumns(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $this->ac->getColumns());
    }
}
