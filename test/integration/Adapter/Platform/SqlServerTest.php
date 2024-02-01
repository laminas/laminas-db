<?php

namespace LaminasIntegrationTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Platform\SqlServer;
use PDO;
use PHPUnit\Framework\TestCase;

use function extension_loaded;
use function getenv;
use function sqlsrv_connect;
use function sqlsrv_errors;
use function var_dump;

/**
 * @group integration
 * @group integration-sqlserver
 */
class SqlServerTest extends TestCase
{
    /** @var array<string, resource> */
    public $adapters = [];

    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV')) {
            $this->markTestSkipped(self::class . ' integration tests are not enabled!');
        }

        $database = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE');
        $database = $database === false ? null : $database;

        if (extension_loaded('sqlsrv')) {
            $this->adapters['sqlsrv'] = sqlsrv_connect(
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME'),
                [
                    'UID'                    => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME'),
                    'PWD'                    => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD'),
                    'Database'               => $database,
                    'TrustServerCertificate' => 1,
                ]
            );
            if (! $this->adapters['sqlsrv']) {
                var_dump(sqlsrv_errors());
                exit;
            }
        }
        if (extension_loaded('pdo') && extension_loaded('pdo_sqlsrv')) {
            $this->adapters['pdo_sqlsrv'] = new PDO(
                'sqlsrv:Server='
                    . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME')
                . ';Database=' . ($database ?: '') . ';TrustServerCertificate=1',
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME'),
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD')
            );
        }
    }

    public function testQuoteValueWithSqlServer()
    {
        if (! isset($this->adapters['pdo_sqlsrv'])) {
            $this->markTestSkipped('SQLServer (pdo_sqlsrv) not configured in unit test configuration file');
        }

        $db    = new SqlServer($this->adapters['pdo_sqlsrv']);
        $value = $db->quoteValue('value');
        self::assertEquals("'value'", $value);
    }
}
