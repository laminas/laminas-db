<?php

namespace LaminasTest\Db\Sql\Platform\Sqlite;

use Laminas\Db\Sql\Platform\Sqlite\SelectDecorator;
use Laminas\Db\Sql\Platform\Sqlite\Sqlite;
use Laminas\Db\Sql\Select;
use PHPUnit\Framework\TestCase;

use function current;
use function key;

class SqliteTest extends TestCase
{
    /**
     * @testdox unit test / object test: Test Sqlite constructor will register the decorator
     * @covers \Laminas\Db\Sql\Platform\Sqlite\Sqlite::__construct
     */
    public function testConstructorRegistersSqliteDecorator()
    {
        $mysql      = new Sqlite();
        $decorators = $mysql->getDecorators();

        $type      = key($decorators);
        $decorator = current($decorators);
        self::assertEquals(Select::class, $type);
        self::assertInstanceOf(SelectDecorator::class, $decorator);
    }
}
