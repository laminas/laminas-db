<?php

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\Sqlsrv;
use Laminas\Db\Adapter\Platform\SqlServer;

/**
 * @group integration
 * @group integration-sqlserver
 */
class SqlServerIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public $adapters = array();

    public function testQuoteValueWithSqlServer()
    {
        if (!$this->adapters['pdo_sqlsrv']) {
            $this->markTestSkipped('SQLServer (pdo_sqlsrv) not configured in unit test configuration file');
        }
        $sqlite = new SqlServer($this->adapters['pdo_sqlsrv']);
        $value = $sqlite->quoteValue('value');
        $this->assertEquals('\'value\'', $value);

    }
}
