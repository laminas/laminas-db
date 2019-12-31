<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Constraint;

use Laminas\Db\Sql\Ddl\Constraint\UniqueKey;
use PHPUnit\Framework\TestCase;

class UniqueKeyTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\UniqueKey::getExpressionData
     */
    public function testGetExpressionData()
    {
        $uk = new UniqueKey('foo', 'my_uk');
        self::assertEquals(
            [[
                'CONSTRAINT %s UNIQUE (%s)',
                ['my_uk', 'foo'],
                [$uk::TYPE_IDENTIFIER, $uk::TYPE_IDENTIFIER],
            ]],
            $uk->getExpressionData()
        );
    }
}
