<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Platform\SqlServer;
use PDO;
use PHPUnit\Framework\TestCase;

use function extension_loaded;
use function getenv;
use function sqlsrv_connect;
use function var_dump;

/**
 * @group integration
 * @group integration-sqlserver
 */
class SqlServerTest extends TestCase
{
    public $adapters = [];

    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV')) {
            $this->markTestSkipped(self::class . ' integration tests are not enabled!');
        }
        if (extension_loaded('sqlsrv')) {
            $this->adapters['sqlsrv'] = sqlsrv_connect(
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME'),
                [
                    'UID'      => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME'),
                    'PWD'      => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD'),
                    'Database' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE') ? : null,
                ]
            );
            if (! $this->adapters['sqlsrv']) {
                var_dump(sqlsrv_errors());
                exit;
            }
        }
        if (extension_loaded('pdo')) {
            $this->adapters['pdo_sqlsrv'] = new PDO(
                'sqlsrv:Server=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME')
                    . ';Database=' . (getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE') ? : null),
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME'),
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD')
            );
        }
    }

    public function testQuoteValueWithSqlServer()
    {
        if (! $this->adapters['pdo_sqlsrv']) {
            $this->markTestSkipped('SQLServer (pdo_sqlsrv) not configured in unit test configuration file');
        }
        $sqlite = new SqlServer($this->adapters['pdo_sqlsrv']);
        $value  = $sqlite->quoteValue('value');
        self::assertEquals('\'value\'', $value);
    }
}
