<?php

namespace LaminasTest\Db\Adapter\Driver\Mysqli;

use Laminas\Db\Adapter\Driver\Mysqli\Connection;
use Laminas\Db\Adapter\Driver\Mysqli\Mysqli;
use Laminas\Db\Adapter\Exception\RuntimeException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function getenv;

use const MYSQLI_CLIENT_SSL;
use const MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;

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
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL')) {
            $this->markTestSkipped('Mysqli test disabled');
        }
        $this->connection = new Connection([]);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Mysqli\Connection::setDriver
     */
    public function testSetDriver()
    {
        self::assertEquals($this->connection, $this->connection->setDriver(new Mysqli([])));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Mysqli\Connection::setConnectionParameters
     */
    public function testSetConnectionParameters()
    {
        self::assertEquals($this->connection, $this->connection->setConnectionParameters([]));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Mysqli\Connection::getConnectionParameters
     */
    public function testGetConnectionParameters()
    {
        $this->connection->setConnectionParameters(['foo' => 'bar']);
        self::assertEquals(['foo' => 'bar'], $this->connection->getConnectionParameters());
    }

    public function testNonSecureConnection()
    {
        $mysqli     = $this->createMockMysqli(0);
        $connection = $this->createMockConnection(
            $mysqli,
            [
                'hostname' => 'localhost',
                'username' => 'superuser',
                'password' => '1234',
                'database' => 'main',
                'port'     => 123,
            ]
        );

        $connection->connect();
    }

    public function testSslConnection()
    {
        $mysqli     = $this->createMockMysqli(MYSQLI_CLIENT_SSL);
        $connection = $this->createMockConnection(
            $mysqli,
            [
                'hostname' => 'localhost',
                'username' => 'superuser',
                'password' => '1234',
                'database' => 'main',
                'port'     => 123,
                'use_ssl'  => true,
            ]
        );

        $connection->connect();
    }

    public function testSslConnectionNoVerify()
    {
        $mysqli     = $this->createMockMysqli(MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
        $connection = $this->createMockConnection(
            $mysqli,
            [
                'hostname'       => 'localhost',
                'username'       => 'superuser',
                'password'       => '1234',
                'database'       => 'main',
                'port'           => 123,
                'use_ssl'        => true,
                'driver_options' => [
                    MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT => true,
                ],
            ]
        );

        $connection->connect();
    }

    public function testConnectionFails()
    {
        $connection = new Connection([]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Connection error');
        $connection->connect();
    }

    /**
     * Create a mock mysqli
     *
     * @param int $flags Expected flags to real_connect
     * @return MockObject
     */
    protected function createMockMysqli($flags)
    {
        $mysqli = $this->getMockBuilder(\mysqli::class)->getMock();
        $mysqli->expects($flags ? $this->once() : $this->never())
            ->method('ssl_set')
            ->with(
                $this->equalTo(''),
                $this->equalTo(''),
                $this->equalTo(''),
                $this->equalTo(''),
                $this->equalTo('')
            );

        if ($flags === 0) {
            // Do not pass $flags argument if invalid flags provided
            $mysqli->expects($this->once())
                ->method('real_connect')
                ->with(
                    $this->equalTo('localhost'),
                    $this->equalTo('superuser'),
                    $this->equalTo('1234'),
                    $this->equalTo('main'),
                    $this->equalTo(123),
                    $this->equalTo('')
                )
                ->willReturn(true);
            return $mysqli;
        }

        $mysqli->expects($this->once())
            ->method('real_connect')
            ->with(
                $this->equalTo('localhost'),
                $this->equalTo('superuser'),
                $this->equalTo('1234'),
                $this->equalTo('main'),
                $this->equalTo(123),
                $this->equalTo(''),
                $this->equalTo($flags)
            )
            ->willReturn(true);

        return $mysqli;
    }

    /**
     * Create a mock connection
     *
     * @param MockObject $mysqli Mock mysqli object
     * @param array                                    $params Connection params
     * @return MockObject
     */
    protected function createMockConnection($mysqli, $params)
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->setMethods(['createResource'])
            ->setConstructorArgs([$params])
            ->getMock();
        $connection->expects($this->once())
            ->method('createResource')
            ->willReturn($mysqli);

        return $connection;
    }
}
