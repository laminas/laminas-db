<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Platform\SqlServer;

use Laminas\Db\Sql\Platform\SqlServer\SelectDecorator;
use Laminas\Db\Sql\Platform\SqlServer\SqlServer;
use Laminas\Db\Sql\Select;
use PHPUnit\Framework\TestCase;

use function current;
use function key;

class SqlServerTest extends TestCase
{
    /**
     * @testdox unit test / object test: Test SqlServer object has Select proxy
     * @covers \Laminas\Db\Sql\Platform\SqlServer\SqlServer::__construct
     */
    public function testConstruct()
    {
        $sqlServer  = new SqlServer();
        $decorators = $sqlServer->getDecorators();

        $type      = key($decorators);
        $decorator = current($decorators);
        self::assertEquals(Select::class, $type);
        self::assertInstanceOf(SelectDecorator::class, $decorator);
    }
}
