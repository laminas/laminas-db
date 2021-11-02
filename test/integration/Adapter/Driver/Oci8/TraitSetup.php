<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\Oci8\Connection;
use Laminas\Db\Adapter\Platform\Oracle;
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
        $driverConfig = [
            'driver' => 'Oci8',
            'hostname' => $this->variables['hostname'],
            'username' => $this->variables['username'],
            'password' => $this->variables['password'],
            'charset' => 'AL32UTF8',
            'platform_options' => [
                'quote_identifiers' => false
            ],
        ];
        //$platform = new Oracle();
        $platform = null;
        $queryResultPrototype = null;
        $profiler = null;
        $adapter = new Adapter($driverConfig, $platform, $queryResultPrototype, $profiler);
        return $adapter;
    }

    /**
     *
     * @return Adapter
     */
    protected function createAdapterWithoutQuoteIdentifiers(): Adapter
    {
        /**
         * @see \Laminas\Db\Adapter::createDriver ['oci8', 'pdo']
         * @var $driverConfig - a config for oracle 'oci8' driver.
         */
        $driverConfig = [
            'driver' => 'oci8',
            'database' => $this->variables['database'],
            'hostname' => $this->variables['hostname'],
            'username' => $this->variables['username'],
            'password' => $this->variables['password'],
// commented quote 'quote_identifiers' - will be errors during execute SQL
//            'platform_options' => [
//                'quote_identifiers' => false,
//            ],
        ];
        $platform = null;
        $queryResultPrototype = null;
        $profiler = null;

        $adapter = new Adapter($driverConfig, $platform, $queryResultPrototype, $profiler);
        return $adapter;
    }


}
