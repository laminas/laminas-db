<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Timestamp;
use PHPUnit\Framework\TestCase;

class TimestampTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Timestamp::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Timestamp('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'TIMESTAMP'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
