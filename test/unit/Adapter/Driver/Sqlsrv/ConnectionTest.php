<?php

namespace LaminasTest\Db\Adapter\Driver\Sqlsrv;

use Laminas\Db\Adapter\Driver\Sqlsrv\Connection;
use Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv;
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
    protected function setUp(): void
    {
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
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::setDriver
     */
    public function testSetDriver()
    {
        self::assertEquals($this->connection, $this->connection->setDriver(new Sqlsrv([])));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::setConnectionParameters
     */
    public function testSetConnectionParameters()
    {
        self::assertEquals($this->connection, $this->connection->setConnectionParameters([]));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::getConnectionParameters
     */
    public function testGetConnectionParameters()
    {
        $this->connection->setConnectionParameters(['foo' => 'bar']);
        self::assertEquals(['foo' => 'bar'], $this->connection->getConnectionParameters());
    }
}
