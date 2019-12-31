<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Platform\Sqlite;

use Laminas\Db\Sql\Platform\Sqlite\Sqlite;

class SqliteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox unit test / object test: Test Sqlite constructor will register the decorator
     * @covers Laminas\Db\Sql\Platform\Sqlite\Sqlite::__construct
     */
    public function testConstructorRegistersSqliteDecorator()
    {
        $mysql = new Sqlite;
        $decorators = $mysql->getDecorators();

        list($type, $decorator) = each($decorators);
        $this->assertEquals('Laminas\Db\Sql\Select', $type);
        $this->assertInstanceOf('Laminas\Db\Sql\Platform\Sqlite\SelectDecorator', $decorator);
    }
}
