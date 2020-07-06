<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Mysqli;

use Laminas\Db\Adapter\Driver\Mysqli\Connection;
use Laminas\Db\Adapter\Driver\Mysqli\Mysqli;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
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
    protected function tearDown()
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
        $mysqli = $this->createMockMysqli(0);
        $connection = $this->createMockConnection(
            $mysqli,
            [
                'hostname' => 'localhost',
                'username' => 'superuser',
                'password' => '1234',
                'database' => 'main',
                'port' => 123,
            ]
        );

        $connection->connect();
    }

    public function testSslConnection()
    {
        $mysqli = $this->createMockMysqli(MYSQLI_CLIENT_SSL);
        $connection = $this->createMockConnection(
            $mysqli,
            [
                'hostname' => 'localhost',
                'username' => 'superuser',
                'password' => '1234',
                'database' => 'main',
                'port' => 123,
                'use_ssl' => true,
            ]
        );

        $connection->connect();
    }

    public function testSslConnectionNoVerify()
    {
        $mysqli = $this->createMockMysqli(MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
        $connection = $this->createMockConnection(
            $mysqli,
            [
                'hostname' => 'localhost',
                'username' => 'superuser',
                'password' => '1234',
                'database' => 'main',
                'port' => 123,
                'use_ssl' => true,
                'driver_options' => [
                    MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT => true
                ],
            ]
        );

        $connection->connect();
    }

    public function testConnectionFails()
    {
        $connection = new Connection([]);

        $this->expectException('\Laminas\Db\Adapter\Exception\RuntimeException');
        $this->expectExceptionMessage('Connection error');
        $connection->connect();
    }

    /**
     * Create a mock mysqli
     *
     * @param int $flags Expected flags to real_connect
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createMockMysqli($flags)
    {
        $mysqli = $this->getMockBuilder('\mysqli')->getMock();
        $mysqli->expects($this->once())
            ->method('init');
        $mysqli->expects($flags ? $this->once() : $this->never())
            ->method('ssl_set')
            ->with(
                $this->equalTo(null),
                $this->equalTo(null),
                $this->equalTo(null),
                $this->equalTo(null),
                $this->equalTo(null)
            );

        $mysqli->expects($this->once())
            ->method('real_connect')
            ->with(
                $this->equalTo('localhost'),
                $this->equalTo('superuser'),
                $this->equalTo('1234'),
                $this->equalTo('main'),
                $this->equalTo(123),
                $this->equalTo(null),
                $this->equalTo($flags)
            )
            ->willReturn(null);

        return $mysqli;
    }

    /**
     * Create a mock connection
     *
     * @param \PHPUnit\Framework\MockObject\MockObject $mysqli Mock mysqli object
     * @param array                                    $params Connection params
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createMockConnection($mysqli, $params)
    {
        $connection = $this->getMockBuilder('\Laminas\Db\Adapter\Driver\Mysqli\Connection')
            ->setMethods(['createResource'])
            ->setConstructorArgs([$params])
            ->getMock();
        $connection->expects($this->once())
            ->method('createResource')
            ->willReturn($mysqli);

        return $connection;
    }
}
