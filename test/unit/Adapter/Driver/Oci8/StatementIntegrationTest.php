<?php

namespace LaminasTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Driver\Oci8\Oci8;
use Laminas\Db\Adapter\Driver\Oci8\Result;
use Laminas\Db\Adapter\Driver\Oci8\Statement;
use PHPUnit\Framework\TestCase;

use function extension_loaded;
use function get_resource_type;
use function getenv;

/**
 * @group integration
 * @group integration-oracle
 */
class StatementIntegrationTest extends TestCase
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
            $this->fail('The phpunit group integration-oracle was enabled, but the extension is not loaded.');
        }
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Statement::initialize
     */
    public function testInitialize()
    {
        $ociResource = oci_connect(
            $this->variables['username'],
            $this->variables['password'],
            $this->variables['hostname']
        );

        $statement = new Statement();
        self::assertSame($statement, $statement->initialize($ociResource));
        unset($stmtResource, $ociResource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Statement::getResource
     */
    public function testGetResource()
    {
        $ociResource = oci_connect(
            $this->variables['username'],
            $this->variables['password'],
            $this->variables['hostname']
        );

        $statement = new Statement();
        $statement->initialize($ociResource);
        $statement->prepare('SELECT * FROM DUAL');
        $resource = $statement->getResource();
        self::assertEquals('oci8 statement', get_resource_type($resource));
        unset($resource, $ociResource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Statement::prepare
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Statement::isPrepared
     */
    public function testPrepare()
    {
        $ociResource = oci_connect(
            $this->variables['username'],
            $this->variables['password'],
            $this->variables['hostname']
        );

        $statement = new Statement();
        $statement->initialize($ociResource);
        self::assertFalse($statement->isPrepared());
        self::assertSame($statement, $statement->prepare('SELECT * FROM DUAL'));
        self::assertTrue($statement->isPrepared());
        unset($resource, $ociResource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Statement::execute
     */
    public function testExecute()
    {
        $oci8      = new Oci8($this->variables);
        $statement = $oci8->createStatement('SELECT * FROM DUAL');
        self::assertSame($statement, $statement->prepare());

        $result = $statement->execute();
        self::assertInstanceOf(Result::class, $result);

        unset($resource, $oci8);
    }
}
