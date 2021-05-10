<?php

namespace LaminasTest\Db\Sql\Platform\Sqlite;

use Laminas\Db\Sql\Platform\Sqlite\Sqlite;
use PHPUnit\Framework\TestCase;

class SqliteTest extends TestCase
{
    /**
     * @testdox unit test / object test: Test Sqlite constructor will register the decorator
     * @covers \Laminas\Db\Sql\Platform\Sqlite\Sqlite::__construct
     */
    public function testConstructorRegistersSqliteDecorator()
    {
        $mysql = new Sqlite;
        $decorators = $mysql->getDecorators();

        $type = key($decorators);
        $decorator = current($decorators);
        self::assertEquals('Laminas\Db\Sql\Select', $type);
        self::assertInstanceOf('Laminas\Db\Sql\Platform\Sqlite\SelectDecorator', $decorator);
    }
}
