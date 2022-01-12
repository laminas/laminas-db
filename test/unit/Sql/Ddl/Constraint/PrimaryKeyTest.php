<?php

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
            [
                [
                    'PRIMARY KEY (%s)',
                    ['foo'],
                    [$pk::TYPE_IDENTIFIER],
                ],
            ],
            $pk->getExpressionData()
        );
    }
}
