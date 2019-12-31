<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Float as FloatColumn;

class FloatTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $this->markTestSkipped('Cannot test Float column under PHP 7; reserved keyword');
        }
    }

    public function testRaisesDeprecationNoticeOnInstantiation()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Deprecated');
        new FloatColumn('foo', 10, 5);
    }
}
