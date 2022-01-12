<?php

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Float as FloatColumn;
use PHPUnit\Framework\Error\Deprecated;
use PHPUnit\Framework\TestCase;

use function version_compare;

use const PHP_VERSION;

class FloatTest extends TestCase
{
    protected function setUp(): void
    {
        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $this->markTestSkipped('Cannot test Float column under PHP 7; reserved keyword');
        }
    }

    public function testRaisesDeprecationNoticeOnInstantiation()
    {
        $this->expectException(Deprecated::class);
        new FloatColumn('foo', 10, 5);
    }
}
