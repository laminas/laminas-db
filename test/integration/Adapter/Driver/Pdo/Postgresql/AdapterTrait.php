<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Postgresql;

use Laminas\Db\Adapter\Adapter;

use function getenv;

trait AdapterTrait
{
    /**
     * @var Adapter
     */
    protected $adapter;

    protected function setUp(): void
    {
        if (!filter_var(getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL'), FILTER_VALIDATE_BOOLEAN)) {
            $this->markTestSkipped('pdo_pgsql integration tests are not enabled!');
        }

        $this->adapter = new Adapter([
            'driver'   => 'pdo_pgsql',
            'database' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE'),
            'hostname' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME'),
            'username' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_USERNAME'),
            'password' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_PASSWORD'),
        ]);
    }

    /** @return null|string */
    protected function getHostname()
    {
        return getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME');
    }
}
