<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Mysqli;

use Laminas\Db\Adapter\Driver\Mysqli\Connection;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 * @group integration-mysqli
 */
class ConnectionTest extends TestCase
{

    protected $variables = [
        'hostname' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME',
        'username' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_USERNAME',
        'password' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PASSWORD',
        'database' => 'TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE',
    ];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
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
    }

    public function testConnectionOk()
    {
        $connection = new Connection($this->variables);
        $connection->connect();

        self::assertTrue($connection->isConnected());
        $connection->disconnect();
    }
}
