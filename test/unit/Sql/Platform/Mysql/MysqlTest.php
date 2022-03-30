<?php

namespace LaminasTest\Db\Sql\Platform\Mysql;

use Laminas\Db\Sql\Platform\Mysql\Mysql;
use Laminas\Db\Sql\Platform\Mysql\SelectDecorator;
use Laminas\Db\Sql\Select;
use PHPUnit\Framework\TestCase;

use function current;
use function key;

class MysqlTest extends TestCase
{
    /**
     * @testdox unit test / object test: Test Mysql object has Select proxy
     * @covers \Laminas\Db\Sql\Platform\Mysql\Mysql::__construct
     */
    public function testConstruct()
    {
        $mysql      = new Mysql();
        $decorators = $mysql->getDecorators();

        $type      = key($decorators);
        $decorator = current($decorators);
        self::assertEquals(Select::class, $type);
        self::assertInstanceOf(SelectDecorator::class, $decorator);
    }
}
