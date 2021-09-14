<?php

namespace LaminasIntegrationTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\Mysqli;
use Laminas\Db\Adapter\Driver\Pdo;
use Laminas\Db\Adapter\Platform\Mysql;
use PHPUnit\Framework\TestCase;

use function extension_loaded;
use function getenv;

/**
 * @group integration
 * @group integration-mysql
 */
class MysqlTest extends TestCase
{
    /** @var array<string, resource|\PDO> */
    public $adapters = [];

    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL')) {
            $this->markTestSkipped(self::class . ' integration tests are not enabled!');
        }
        if (extension_loaded('mysqli')) {
            $this->adapters['mysqli'] = new \mysqli(
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME'),
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_USERNAME'),
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PASSWORD'),
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE')
            );
        }
        if (extension_loaded('pdo')) {
            $this->adapters['pdo_mysql'] = new \PDO(
                'mysql:host=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME') . ';dbname='
                . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE'),
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_USERNAME'),
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PASSWORD')
            );
        }
    }

    public function testQuoteValueWithMysqli()
    {
        if (! $this->adapters['mysqli'] instanceof \Mysqli) {
            $this->markTestSkipped('MySQL (Mysqli) not configured in unit test configuration file');
        }
        $mysql = new Mysql($this->adapters['mysqli']);
        $value = $mysql->quoteValue('value');
        self::assertEquals('\'value\'', $value);

        $mysql = new Mysql(new Mysqli\Mysqli(new Mysqli\Connection($this->adapters['mysqli'])));
        $value = $mysql->quoteValue('value');
        self::assertEquals('\'value\'', $value);
    }

    public function testQuoteValueWithPdoMysql()
    {
        if (! $this->adapters['pdo_mysql'] instanceof \PDO) {
            $this->markTestSkipped('MySQL (PDO_Mysql) not configured in unit test configuration file');
        }
        $mysql = new Mysql($this->adapters['pdo_mysql']);
        $value = $mysql->quoteValue('value');
        self::assertEquals('\'value\'', $value);

        $mysql = new Mysql(new Pdo\Pdo(new Pdo\Connection($this->adapters['pdo_mysql'])));
        $value = $mysql->quoteValue('value');
        self::assertEquals('\'value\'', $value);
    }
}
