<?php

namespace LaminasTest\Db\Sql\Ddl\Constraint;

use Laminas\Db\Sql\Ddl\Constraint\Check;
use PHPUnit\Framework\TestCase;

class CheckTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Constraint\Check::getExpressionData
     */
    public function testGetExpressionData()
    {
        $check = new Check('id>0', 'foo');
        self::assertEquals(
            [
                [
                    'CONSTRAINT %s CHECK (%s)',
                    ['foo', 'id>0'],
                    [$check::TYPE_IDENTIFIER, $check::TYPE_LITERAL],
                ],
            ],
            $check->getExpressionData()
        );
    }
}
