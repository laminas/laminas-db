<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

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

    protected function getHostname()
    {
        return getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME');
    }
}
