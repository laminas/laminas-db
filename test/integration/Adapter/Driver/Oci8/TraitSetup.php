<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\Oci8\Connection;
use function extension_loaded;
use function getenv;
use function sprintf;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.Trait.Suffix
trait TraitSetup
{
    /** @var array<string, string> */
    protected $variables = [
        'hostname' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_HOSTNAME',
        'username' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_USERNAME',
        'password' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_PASSWORD',
        'database' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_DATABASE',
    ];

//    /** @var array<string, string> */
//    protected $optional = [
//        'port' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_PORT',
//    ];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if (!getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8')) {
            $this->markTestSkipped('Mysqli integration test disabled');
        }

        if (!extension_loaded('oci8')) {
            $this->fail('The phpunit group integration-mysqli was enabled, but the extension "oci8" is not loaded.');
        }

        foreach ($this->variables as $name => $value) {
            if (!getenv($value)) {
                $this->markTestSkipped(sprintf(
                    'Missing required variable %s from phpunit.xml for this integration test',
                    $value
                ));
            }
            $this->variables[$name] = getenv($value);
        }
        $this->variables['hostname'] .= '/' . $this->variables['database'];
        unset ($this->variables['database']);

//        foreach ($this->optional as $name => $value) {
//            if (getenv($value)) {
//                $this->variables[$name] = getenv($value);
//            }
//        }
    }

    /**
     * @return Connection
     */
    protected function createConnection(): Connection
    {
        $connection = new Connection($this->variables);
        return $connection;
    }

    /**
     * @return Adapter
     */
    protected function createAdapter(): Adapter
    {
        $adapter = new Adapter([
            'driver' => 'oci8',
            'database' => $this->variables['database'],
            'hostname' => $this->variables['hostname'],
            'username' => $this->variables['username'],
            'password' => $this->variables['password'],
            'options' => [
                'buffer_results' => false
            ],
        ]);
        return $adapter;
    }
}
