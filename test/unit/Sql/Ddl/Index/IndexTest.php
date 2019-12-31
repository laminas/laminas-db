<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Index;

use Laminas\Db\Sql\Ddl\Index\Index;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Index\Index::getExpressionData
     */
    public function testGetExpressionData()
    {
        $uk = new Index('foo', 'my_uk');
        self::assertEquals(
            [[
                'INDEX %s(%s)',
                ['my_uk', 'foo'],
                [$uk::TYPE_IDENTIFIER, $uk::TYPE_IDENTIFIER],
            ]],
            $uk->getExpressionData()
        );
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Index\Index::getExpressionData
     */
    public function testGetExpressionDataWithLength()
    {
        $key = new Index(['foo', 'bar'], 'my_uk', [10, 5]);
        self::assertEquals(
            [[
                'INDEX %s(%s(10), %s(5))',
                ['my_uk', 'foo', 'bar'],
                [$key::TYPE_IDENTIFIER, $key::TYPE_IDENTIFIER, $key::TYPE_IDENTIFIER],
            ]],
            $key->getExpressionData()
        );
    }

    /**
     * @covers \Laminas\Db\Sql\Ddl\Index\Index::getExpressionData
     */
    public function testGetExpressionDataWithLengthUnmatched()
    {
        $key = new Index(['foo', 'bar'], 'my_uk', [10]);
        self::assertEquals(
            [[
                'INDEX %s(%s(10), %s)',
                ['my_uk', 'foo', 'bar'],
                [$key::TYPE_IDENTIFIER, $key::TYPE_IDENTIFIER, $key::TYPE_IDENTIFIER],
            ]],
            $key->getExpressionData()
        );
    }
}
