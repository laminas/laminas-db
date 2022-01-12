<?php

namespace LaminasTest\Db\Adapter\Driver\IbmDb2;

use PHPUnit\Framework\TestCase;

use function extension_loaded;
use function getenv;

abstract class AbstractIntegrationTest extends TestCase
{
    /** @var array */
    protected $variables = [
        'database' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_IBMDB2_DATABASE',
        'username' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_IBMDB2_USERNAME',
        'password' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_IBMDB2_PASSWORD',
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

        if (! extension_loaded('ibm_db2')) {
            $this->fail('The phpunit group integration-ibm_db2 was enabled, but the extension is not loaded.');
        }
    }
}
