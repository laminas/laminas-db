<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Mysqli;

use function extension_loaded;
use function getenv;
use function sprintf;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.Trait.Suffix
trait TraitSetup
{
    /** @var array<string, string> */
    protected $variables = [
        'hostname' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME',
        'username' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_USERNAME',
        'password' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PASSWORD',
        'database' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE',
    ];

    /** @var array<string, string> */
    protected $optional = [
        'port' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PORT',
    ];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL')) {
            $this->markTestSkipped('Mysqli integration test disabled');
        }

        if (! extension_loaded('mysqli')) {
            $this->fail('The phpunit group integration-mysqli was enabled, but the extension is not loaded.');
        }

        foreach ($this->variables as $name => $value) {
            if (! getenv($value)) {
                $this->markTestSkipped(sprintf(
                    'Missing required variable %s from phpunit.xml for this integration test',
                    $value
                ));
            }
            $this->variables[$name] = getenv($value);
        }

        foreach ($this->optional as $name => $value) {
            if (getenv($value)) {
                $this->variables[$name] = getenv($value);
            }
        }
    }
}
