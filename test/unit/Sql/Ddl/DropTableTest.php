<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl;

use Laminas\Db\Sql\Ddl\DropTable;
use Laminas\Db\Sql\TableIdentifier;
use PHPUnit\Framework\TestCase;

class DropTableTest extends TestCase
{
    /**
     * @covers \Laminas\Db\Sql\Ddl\DropTable::getSqlString
     */
    public function testGetSqlString()
    {
        $dt = new DropTable('foo');
        self::assertEquals('DROP TABLE "foo"', $dt->getSqlString());

        $dt = new DropTable(new TableIdentifier('foo'));
        self::assertEquals('DROP TABLE "foo"', $dt->getSqlString());

        $dt = new DropTable(new TableIdentifier('bar', 'foo'));
        self::assertEquals('DROP TABLE "foo"."bar"', $dt->getSqlString());
    }
}
