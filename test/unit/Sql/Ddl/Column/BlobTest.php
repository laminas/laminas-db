<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Blob;
use PHPUnit\Framework\TestCase;

class BlobTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\Column\Blob::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Blob('foo');
        self::assertEquals(
            [['%s %s NOT NULL', ['foo', 'BLOB'], [$column::TYPE_IDENTIFIER, $column::TYPE_LITERAL]]],
            $column->getExpressionData()
        );
    }
}
