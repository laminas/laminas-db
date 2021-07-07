<?php

namespace LaminasIntegrationTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\Pdo;
use Laminas\Db\Adapter\Driver\Pgsql;
use Laminas\Db\Adapter\Platform\Postgresql;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 * @group integration-postgres
 */
class PostgresqlTest extends TestCase
{
    public $adapters = [];

    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL')) {
            $this->markTestSkipped(__CLASS__ . ' integration tests are not enabled!');
        }
        if (extension_loaded('pgsql')) {
            $this->adapters['pgsql'] = \pg_connect(
                'host=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME')
                    . ' dbname=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE')
                    . ' user=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_USERNAME')
                    . ' password=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_PASSWORD')
            );
        }
        if (extension_loaded('pdo')) {
            $this->adapters['pdo_pgsql'] = new \PDO(
                'pgsql:host=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME') . ';dbname='
                . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE'),
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_USERNAME'),
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_PASSWORD')
            );
        }
    }

    public function testQuoteValueWithPgsql()
    {
        if (! is_resource($this->adapters['pgsql'])) {
            $this->markTestSkipped('Postgres (pgsql) not configured in unit test configuration file');
        }
        $pgsql = new Postgresql($this->adapters['pgsql']);
        $value = $pgsql->quoteValue('value');
        self::assertEquals('\'value\'', $value);

        $pgsql = new Postgresql(new Pgsql\Pgsql(new Pgsql\Connection($this->adapters['pgsql'])));
        $value = $pgsql->quoteValue('value');
        self::assertEquals('\'value\'', $value);
    }

    public function testQuoteValueWithPdoPgsql()
    {
        if (! $this->adapters['pdo_pgsql'] instanceof \PDO) {
            $this->markTestSkipped('Postgres (PDO_PGSQL) not configured in unit test configuration file');
        }
        $pgsql = new Postgresql($this->adapters['pdo_pgsql']);
        $value = $pgsql->quoteValue('value');
        self::assertEquals('\'value\'', $value);

        $pgsql = new Postgresql(new Pdo\Pdo(new Pdo\Connection($this->adapters['pdo_pgsql'])));
        $value = $pgsql->quoteValue('value');
        self::assertEquals('\'value\'', $value);
    }
}
