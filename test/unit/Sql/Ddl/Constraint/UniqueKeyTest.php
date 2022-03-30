<?php

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
            [
                [
                    'CONSTRAINT %s UNIQUE (%s)',
                    ['my_uk', 'foo'],
                    [$uk::TYPE_IDENTIFIER, $uk::TYPE_IDENTIFIER],
                ],
            ],
            $uk->getExpressionData()
        );
    }
}
