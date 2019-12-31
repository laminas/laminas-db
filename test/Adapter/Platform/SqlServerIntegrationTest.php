<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Platform\SqlServer;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 * @group integration-sqlserver
 */
class SqlServerIntegrationTest extends TestCase
{
    public $adapters = [];

    public function testQuoteValueWithSqlServer()
    {
        if (! $this->adapters['pdo_sqlsrv']) {
            $this->markTestSkipped('SQLServer (pdo_sqlsrv) not configured in unit test configuration file');
        }
        $sqlite = new SqlServer($this->adapters['pdo_sqlsrv']);
        $value = $sqlite->quoteValue('value');
        self::assertEquals('\'value\'', $value);
    }
}
