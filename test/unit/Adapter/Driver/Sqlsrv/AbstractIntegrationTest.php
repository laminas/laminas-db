<?php

namespace LaminasTest\Db\Adapter\Driver\Sqlsrv;

use PHPUnit\Framework\TestCase;

use function extension_loaded;
use function getenv;
use function sqlsrv_connect;

abstract class AbstractIntegrationTest extends TestCase
{
    /** @var array<string, string> */
    protected $variables = [
        'hostname' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME',
        'username' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME',
        'password' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD',
    ];

    /** @var array<string, resource> */
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

        $this->variables['options'] = ['TrustServerCertificate' => '1'];
        if (! extension_loaded('sqlsrv')) {
            $this->fail('The phpunit group integration-sqlsrv was enabled, but the extension is not loaded.');
        }

        $this->adapters['sqlsrv'] = sqlsrv_connect(
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME'),
            [
                'UID'                    => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME'),
                'PWD'                    => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD'),
                'TrustServerCertificate' => 1,
            ]
        );
    }
}
