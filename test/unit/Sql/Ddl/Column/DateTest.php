<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Date::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Date('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'DATE'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
