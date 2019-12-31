<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl;

use Laminas\Db\Sql\Ddl\DropTable;

class DropTableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Sql\Ddl\DropTable::getSqlString
     */
    public function testGetSqlString()
    {
        $dt = new DropTable('foo');
        $this->assertEquals('DROP TABLE "foo"', $dt->getSqlString());
    }
}
