<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Mysql;

use Laminas\Db\Adapter\Adapter;

use function getenv;

trait AdapterTrait
{
    /** @var $adapter */
    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL')) {
            $this->markTestSkipped('pdo_mysql integration tests are not enabled!');
        }

        $this->adapter = new Adapter([
            'driver'   => 'pdo_mysql',
            'database' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE'),
            'hostname' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME'),
            'username' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_USERNAME'),
            'password' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PASSWORD'),
        ]);
    }

    /** @return null|string */
    protected function getHostname()
    {
        return getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME');
    }
}
