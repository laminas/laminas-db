<?php

namespace LaminasTest\Db\Adapter\Driver\IbmDb2;

use Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2;
use Laminas\Db\Adapter\Driver\IbmDb2\Result;
use Laminas\Db\Adapter\Driver\IbmDb2\Statement;
use Laminas\Db\Adapter\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

use function extension_loaded;
use function get_resource_type;
use function getenv;

/**
 * @group integration
 * @group integration-ibm_db2
 */
class StatementIntegrationTest extends TestCase
{
    /** @var array<string, string> */
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

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::initialize
     */
    public function testInitialize()
    {
        $db2Resource = db2_connect(
            $this->variables['database'],
            $this->variables['username'],
            $this->variables['password']
        );

        $statement = new Statement();
        self::assertSame($statement, $statement->initialize($db2Resource));
        unset($stmtResource, $db2Resource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::getResource
     */
    public function testGetResource()
    {
        $db2Resource = db2_connect(
            $this->variables['database'],
            $this->variables['username'],
            $this->variables['password']
        );

        $statement = new Statement();
        $statement->initialize($db2Resource);
        $statement->prepare("SELECT 'foo' FROM sysibm.sysdummy1");
        $resource = $statement->getResource();
        self::assertEquals('DB2 Statement', get_resource_type($resource));
        unset($resource, $db2Resource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::prepare
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::isPrepared
     */
    public function testPrepare()
    {
        $db2Resource = db2_connect(
            $this->variables['database'],
            $this->variables['username'],
            $this->variables['password']
        );

        $statement = new Statement();
        $statement->initialize($db2Resource);
        self::assertFalse($statement->isPrepared());
        self::assertSame($statement, $statement->prepare("SELECT 'foo' FROM SYSIBM.SYSDUMMY1"));
        self::assertTrue($statement->isPrepared());
        unset($resource, $db2Resource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::prepare
     */
    public function testPrepareThrowsAnExceptionOnFailure()
    {
        $db2Resource = db2_connect(
            $this->variables['database'],
            $this->variables['username'],
            $this->variables['password']
        );
        $statement   = new Statement();
        $statement->initialize($db2Resource);
        $this->expectException(RuntimeException::class);
        $statement->prepare("SELECT");
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::execute
     */
    public function testExecute()
    {
        $ibmdb2    = new IbmDb2($this->variables);
        $statement = $ibmdb2->createStatement("SELECT 'foo' FROM SYSIBM.SYSDUMMY1");
        self::assertSame($statement, $statement->prepare());

        $result = $statement->execute();
        self::assertInstanceOf(Result::class, $result);

        unset($resource, $ibmdb2);
    }
}
