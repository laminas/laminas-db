<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Boolean;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Boolean::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Boolean('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'BOOLEAN'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Boolean
     *
     * @group 6257
     */
    public function testIsAlwaysNotNullable()
    {
        $column = new Boolean('foo', true);

        self::assertFalse($column->isNullable());

        $column->setNullable(true);

        self::assertFalse($column->isNullable());
    }
}
