<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\IbmDb2;

use Laminas\Db\Adapter\Driver\IbmDb2\Connection;
use Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2;
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
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Connection::setDriver
     */
    public function testSetDriver()
    {
        self::assertEquals($this->connection, $this->connection->setDriver(new IbmDb2([])));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Connection::setConnectionParameters
     */
    public function testSetConnectionParameters()
    {
        self::assertEquals($this->connection, $this->connection->setConnectionParameters([]));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Connection::getConnectionParameters
     */
    public function testGetConnectionParameters()
    {
        $this->connection->setConnectionParameters(['foo' => 'bar']);
        self::assertEquals(['foo' => 'bar'], $this->connection->getConnectionParameters());
    }
}
