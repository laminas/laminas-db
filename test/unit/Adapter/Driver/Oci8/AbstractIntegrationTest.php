<?php

namespace LaminasTest\Db\Adapter\Driver\Oci8;

use PHPUnit\Framework\TestCase;

use function extension_loaded;
use function getenv;

abstract class AbstractIntegrationTest extends TestCase
{
    /** @var array<string, string> */
    protected $variables = [
        'hostname' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_HOSTNAME',
        'username' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_USERNAME',
        'password' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_PASSWORD',
    ];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        foreach ($this->variables as $name => $value) {
            if (! getenv($value)) {
                $this->markTestSkipped(
                    'Missing required variable ' . $value . ' from phpunit.xml for this integration test'
                );
            }
            $this->variables[$name] = getenv($value);
        }

        if (! extension_loaded('oci8')) {
            $this->fail('The phpunit group integration-oci8 was enabled, but the extension is not loaded.');
        }
    }
}
