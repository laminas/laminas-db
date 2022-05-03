<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\Oci8\Connection;
use Laminas\Db\Adapter\Platform;
use Laminas\Db\Adapter\Profiler;
use Laminas\Db\ResultSet;

use function array_merge;
use function extension_loaded;
use function getenv;
use function sprintf;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.Trait.Suffix
trait TraitSetup
{
    /**
     * Options for adapter from "phpunit.xml".
     *
     * @var array $variables
     */
    protected $variables = [
        'hostname' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_HOSTNAME',
        'username' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_USERNAME',
        'password' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_PASSWORD',
        'database' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_DATABASE',
    ];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8')) {
            $this->markTestSkipped('OCI8 integration test disabled');
        }

        if (! extension_loaded('oci8')) {
            $this->fail('The phpunit group integration-oci8 was enabled, but the extension "oci8" is not loaded.');
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
        $this->variables['hostname'] .= '/' . $this->variables['database'];
        unset($this->variables['database']);
    }

    protected function createConnection(): Connection
    {
        return new Connection($this->variables);
    }

    /**
     * @return array
     */
    protected function getDriverConfig()
    {
        return [
            'driver'   => 'Oci8',
            'hostname' => $this->variables['hostname'],
            'username' => $this->variables['username'],
            'password' => $this->variables['password'],
            'charset'  => 'AL32UTF8',
//            'platform_options' => [
//                'quote_identifiers' => true
//            ],
        ];
    }

    protected function createAdapter(
        ?Platform\PlatformInterface $platform = null,
        ?ResultSet\ResultSetInterface $queryResultPrototype = null,
        ?Profiler\ProfilerInterface $profiler = null
    ): Adapter {
        $driverConfig = $this->getDriverConfig();
        return new Adapter($driverConfig, $platform, $queryResultPrototype, $profiler);
    }

    protected function createAdapterWithQuoteIdentifiers(
        ?Platform\PlatformInterface $platform = null,
        ?ResultSet\ResultSetInterface $queryResultPrototype = null,
        ?Profiler\ProfilerInterface $profiler = null
    ): Adapter {
        $driverConfig                                          = $this->getDriverConfig();
        $driverConfig                                          = array_merge([
            'platform_options' => [],
        ], $driverConfig);
        $driverConfig['platform_options']['quote_identifiers'] = true;
        return new Adapter($driverConfig, $platform, $queryResultPrototype, $profiler);
    }

    protected function createAdapterWithoutQuoteIdentifiers(
        ?Platform\PlatformInterface $platform = null,
        ?ResultSet\ResultSetInterface $queryResultPrototype = null,
        ?Profiler\ProfilerInterface $profiler = null
    ): Adapter {
        /**
         * @see \Laminas\Db\Adapter::createDriver ['oci8', 'pdo']
         *
         * @var $driverConfig - a config for oracle 'oci8' driver.
         */
        $driverConfig                                          = $this->getDriverConfig();
        $driverConfig                                          = array_merge([
            'platform_options' => [],
        ], $driverConfig);
        $driverConfig['platform_options']['quote_identifiers'] = false;
        return new Adapter($driverConfig, $platform, $queryResultPrototype, $profiler);
    }
}
