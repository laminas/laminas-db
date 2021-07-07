<?php

namespace LaminasTest\Db\Adapter\Driver\Sqlsrv;

use PHPUnit\Framework\TestCase;

abstract class AbstractIntegrationTest extends TestCase
{
    protected $variables = [
        'hostname' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME',
        'username' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME',
        'password' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD',
    ];
    protected $adapters;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV')) {
            $this->markTestSkipped('SQLSRV tests are not enabled');
        }
        foreach ($this->variables as $name => $value) {
            if (! getenv($value)) {
                $this->markTestSkipped(
                    'Missing required variable ' . $value . ' from phpunit.xml for this integration test'
                );
            }
            $this->variables[$name] = getenv($value);
        }

        if (! extension_loaded('sqlsrv')) {
            $this->fail('The phpunit group integration-sqlsrv was enabled, but the extension is not loaded.');
        }

        $this->adapters['sqlsrv'] = sqlsrv_connect(
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME'),
            [
                'UID' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME'),
                'PWD' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD'),
            ]
        );
    }
}
