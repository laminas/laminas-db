<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Integer;
use Laminas\Db\Sql\Ddl\Constraint\PrimaryKey;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Integer::__construct
     */
    public function testObjectConstruction()
    {
        $integer = new Integer('foo');
        self::assertEquals('foo', $integer->getName());
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Column::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Integer('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'INTEGER'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );

        $column = new Integer('foo');
        $column->addConstraint(new PrimaryKey());
        self::assertEquals(
            [
                ['%s %s NOT NULL', ['foo', 'INTEGER'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]],
                ' ',
                ['PRIMARY KEY', [], []],
            ],
            $column->getExpressionData()
        );
    }
}
