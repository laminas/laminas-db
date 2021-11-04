<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Sqlite;

use Laminas\Db\Adapter\Adapter;

use function getenv;

trait AdapterTrait
{
    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLITE_FILE')) {
            $this->markTestSkipped('pdo_sqlite integration tests are not enabled!');
        }
        $database = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLITE_FILE_DATABASE');
        $dsn = 'sqlite:' . $database;

        $this->adapter = new Adapter([
            'driver' => 'pdo',
            'dsn' => $dsn
        ]);
    }
}
