<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Constraint;

use Laminas\Db\Sql\Ddl\Constraint\PrimaryKey;
use PHPUnit\Framework\TestCase;

class PrimaryKeyTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\PrimaryKey::getExpressionData
     */
    public function testGetExpressionData()
    {
        $pk = new PrimaryKey('foo');
        self::assertEquals(
            [[
                'PRIMARY KEY (%s)',
                ['foo'],
                [$pk::TYPE_IDENTIFIER],
            ]],
            $pk->getExpressionData()
        );
    }
}
