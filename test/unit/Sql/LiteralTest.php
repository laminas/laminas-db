<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql;

use Laminas\Db\Sql\Literal;
use PHPUnit\Framework\TestCase;

class LiteralTest extends TestCase
{
    public function testSetLiteral()
    {
        $literal = new Literal('bar');
        self::assertSame($literal, $literal->setLiteral('foo'));
    }

    public function testGetLiteral()
    {
        $literal = new Literal('bar');
        self::assertEquals('bar', $literal->getLiteral());
    }

    public function testGetExpressionData()
    {
        $literal = new Literal('bar');
        self::assertEquals([['bar', [], []]], $literal->getExpressionData());
    }

    public function testGetExpressionDataWillEscapePercent()
    {
        $expression = new Literal('X LIKE "foo%"');
        self::assertEquals(
            [[
                'X LIKE "foo%%"',
                [],
                [],
            ]],
            $expression->getExpressionData()
        );
    }
}
