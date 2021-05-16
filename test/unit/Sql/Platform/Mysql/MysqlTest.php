<?php

namespace LaminasTest\Db\Sql\Platform\Mysql;

use Laminas\Db\Sql\Platform\Mysql\Mysql;
use PHPUnit\Framework\TestCase;

class MysqlTest extends TestCase
{
    /**
     * @testdox unit test / object test: Test Mysql object has Select proxy
     * @covers \Laminas\Db\Sql\Platform\Mysql\Mysql::__construct
     */
    public function testConstruct()
    {
        $mysql = new Mysql;
        $decorators = $mysql->getDecorators();

        $type = key($decorators);
        $decorator = current($decorators);
        self::assertEquals('Laminas\Db\Sql\Select', $type);
        self::assertInstanceOf('Laminas\Db\Sql\Platform\Mysql\SelectDecorator', $decorator);
    }
}
