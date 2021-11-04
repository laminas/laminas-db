<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Oracle;

use Laminas\Db\Adapter\Adapter;

use function getenv;

trait AdapterTrait
{
    protected $hostname;
    protected $database;
    protected $username;
    protected $password;
    protected $dsn;

    protected function setUp(): void
    {
        if (!getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8')) {
            $this->markTestSkipped('pdo_oci integration tests are not enabled!');
        }
        $this->hostname = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_HOSTNAME');
        $this->database = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_DATABASE');
        $this->username = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_USERNAME');
        $this->password = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_PASSWORD');

        $this->dsn = sprintf('oci:dbname=//%s/%s',
            $this->hostname,
            $this->database
        );
    }

    protected function createAdapter()
    {
        $driverOptions = [
            'driver' => 'pdo_oci',
            'dsn' => $this->dsn,
            'username' => $this->username,
            'password' => $this->password,
            'platform_options' => [
                'quote_identifiers' => false
            ],
        ];
        $adapter = new Adapter($driverOptions);
        return $adapter;
    }
}
