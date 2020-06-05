<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pgsql;

use Laminas\Db\Adapter\Driver\Pgsql\Connection;
use Laminas\Db\Adapter\Exception as AdapterException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

use function extension_loaded;
use function pg_client_encoding;

use const PGSQL_CONNECT_FORCE_NEW;

class ConnectionTest extends TestCase
{
    /** @var Connection */
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
    public function testResource()
    {
        if (! extension_loaded('pgsql')) {
            $this->markTestSkipped('pgsql extension not loaded');
        }

        try {
            $resource = $this->connection->getResource();
            // connected with empty string
            self::assertIsResource($resource);
        } catch (AdapterException\RuntimeException $exc) {
            // If it throws an exception it has failed to connect
            $this->expectException(AdapterException\RuntimeException::class);
            throw $exc;
        }
    }

    /**
     * @group 6760
     * @group 6787
     */
    public function testGetConnectionStringEncodeSpecialSymbol()
    {
        $connectionParameters = [
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'post'     => '5432',
            'dbname'   => 'test',
            'username' => 'test',
            'password' => 'test123!',
        ];

        $this->connection->setConnectionParameters($connectionParameters);

        $getConnectionString = new ReflectionMethod(
            Connection::class,
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

        $this->expectException(AdapterException\InvalidArgumentException::class);
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
        self::assertEquals(
            $type,
            (function ($connection) {
                return $connection->type;
            })->bindTo($connection = $this->connection, $connection)($connection)
        );
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

        $this->expectException(AdapterException\RuntimeException::class);

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
