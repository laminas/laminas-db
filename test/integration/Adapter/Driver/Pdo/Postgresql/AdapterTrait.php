<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Postgresql;

use Laminas\Db\Adapter\Adapter;

trait AdapterTrait
{
    protected function setUp()
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL')) {
            $this->markTestSkipped('pdo_pgsql integration tests are not enabled!');
        }

        $this->adapter = new Adapter([
            'driver'   => 'pdo_pgsql',
            'database' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE'),
            'hostname' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME'),
            'username' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_USERNAME'),
            'password' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_PASSWORD')
        ]);
    }
}
