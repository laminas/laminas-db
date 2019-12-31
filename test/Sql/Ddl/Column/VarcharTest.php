<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Varchar;
use PHPUnit\Framework\TestCase;

class VarcharTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Varchar::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Varchar('foo', 20);
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'VARCHAR(20)'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );

        $column->setDefault('bar');
        self::assertEquals(
            [[
                '%s %s NOT NULL DEFAULT %s',
                ['foo', 'VARCHAR(20)', 'bar'],
                [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL, $column::TYPE_VALUE],
            ]],
            $column->getExpressionData()
        );
    }
}
