<?php

namespace LaminasTest\Db\Adapter\Driver\Pgsql;

use Laminas\Db\Adapter\Driver\Pgsql\Connection;
use Laminas\Db\Adapter\Exception as AdapterException;
use LaminasTest\Db\DeprecatedAssertionsTrait;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ConnectionTest extends TestCase
{
    use DeprecatedAssertionsTrait;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->connection = new Connection();
    }

    /**
     * Test getResource method if it tries to connect to the database.
     *
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Connection::getResource
     */
    public function testResourceInvalid()
    {
        if (! extension_loaded('pgsql')) {
            $this->markTestSkipped('pgsql extension not loaded');
        }

        // invalid port should lead to the custom error handler throwing
        $conn = new Connection(['socket' => '127.0.0.1', 'port' => 65112]);
        try {
            $resource = $conn->getResource();
            $this->fail('should throw');
        } catch (AdapterException\RuntimeException $exc) {
            $this->assertSame(
                'Laminas\Db\Adapter\Driver\Pgsql\Connection::connect: Unable to connect to database',
                $exc->getMessage()
            );
        }
    }

    /**
     * Test getResource method if it tries to connect to the database.
     *
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Connection::getResource
     */
    public function testResource()
    {
        if (! extension_loaded('pgsql')) {
            $this->markTestSkipped('pgsql extension not loaded');
        }

        try {
            $resource = $this->connection->getResource();
            // connected with empty string
            self::assertInternalType('resource', $resource);
        } catch (AdapterException\RuntimeException $exc) {
            // If it throws an exception it has failed to connect
            $this->expectException('Laminas\Db\Adapter\Exception\RuntimeException');
            throw $exc;
        }
    }

    /**
     * Test disconnect method to return instance of ConnectionInterface
     */
    public function testDisconnect()
    {
        include_once 'pgsqlMockFunctions.php';
        self::assertSame($this->connection, $this->connection->disconnect());
    }

    /**
     * @group 6760
     * @group 6787
     */
    public function testGetConnectionStringEncodeSpecialSymbol()
    {
        $connectionParameters = [
            'driver'    => 'pgsql',
            'host' => 'localhost',
            'post' => '5432',
            'dbname' => 'test',
            'username'  => 'test',
            'password'  => 'test123!',
        ];

        $this->connection->setConnectionParameters($connectionParameters);

        $getConnectionString = new ReflectionMethod(
            'Laminas\Db\Adapter\Driver\Pgsql\Connection',
            'getConnectionString'
        );

        $getConnectionString->setAccessible(true);

        self::assertEquals(
            'host=localhost user=test password=test123! dbname=test',
            $getConnectionString->invoke($this->connection)
        );
    }

    public function testSetConnectionTypeException()
    {
        if (! extension_loaded('pgsql')) {
            $this->markTestSkipped('pgsql extension not loaded');
        }

        $this->expectException('\Laminas\Db\Adapter\Exception\InvalidArgumentException');
        $this->connection->setType(3);
    }

    /**
     * Test the connection type setter
     */
    public function testSetConnectionType()
    {
        if (! extension_loaded('pgsql')) {
            $this->markTestSkipped('pgsql extension not loaded');
        }
        $type = PGSQL_CONNECT_FORCE_NEW;
        $this->connection->setType($type);
        self::assertEquals($type, self::readAttribute($this->connection, 'type'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetCharset()
    {
        if (! extension_loaded('pgsql')) {
            $this->markTestSkipped('pgsql extension not loaded');
        }

        $this->connection->setConnectionParameters([
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'post'     => '5432',
            'dbname'   => 'laminasdb_test',
            'username' => 'postgres',
            'password' => 'postgres',
            'charset'  => 'SQL_ASCII',
        ]);

        try {
            $this->connection->connect();
        } catch (AdapterException\RuntimeException $e) {
            $this->markTestSkipped('Skipping pgsql charset test due to inability to connecto to database');
        }

        self::assertEquals('SQL_ASCII', pg_client_encoding($this->connection->getResource()));
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetInvalidCharset()
    {
        if (! extension_loaded('pgsql')) {
            $this->markTestSkipped('pgsql extension not loaded');
        }

        $this->expectException('Laminas\Db\Adapter\Exception\RuntimeException');

        $this->connection->setConnectionParameters([
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'post'     => '5432',
            'dbname'   => 'laminasdb_test',
            'username' => 'postgres',
            'password' => 'postgres',
            'charset'  => 'FOOBAR',
        ]);

        $this->connection->connect();
    }
}
