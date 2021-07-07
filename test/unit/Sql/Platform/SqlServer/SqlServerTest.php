<?php

namespace LaminasTest\Db\Sql\Platform\SqlServer;

use Laminas\Db\Sql\Platform\SqlServer\SqlServer;
use PHPUnit\Framework\TestCase;

class SqlServerTest extends TestCase
{
    /**
     * @testdox unit test / object test: Test SqlServer object has Select proxy
     * @covers \Laminas\Db\Sql\Platform\SqlServer\SqlServer::__construct
     */
    public function testConstruct()
    {
        $sqlServer = new SqlServer;
        $decorators = $sqlServer->getDecorators();

        $type = key($decorators);
        $decorator = current($decorators);
        self::assertEquals('Laminas\Db\Sql\Select', $type);
        self::assertInstanceOf('Laminas\Db\Sql\Platform\SqlServer\SelectDecorator', $decorator);
    }
}
