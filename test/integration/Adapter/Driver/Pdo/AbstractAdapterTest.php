<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Adapter;
use PHPUnit\Framework\TestCase;

/**
 * @property Adapter $adapter
 */
abstract class AbstractAdapterTest extends TestCase
{
    const DB_SERVER_PORT = null;

    /**
     * @covers \Laminas\Db\Adapter\Adapter::__construct()
     */
    public function testConnection()
    {
        $this->assertInstanceOf(Adapter::class, $this->adapter);
    }

    public function testDriverDisconnectAfterQuoteWithPlatform()
    {
        $isTcpConnection = $this->isTcpConnection();

        $this->adapter->getDriver()->getConnection()->connect();

        self::assertTrue($this->adapter->getDriver()->getConnection()->isConnected());
        if ($isTcpConnection) {
            self::assertTrue($this->isConnectedTcp());
        }

        $this->adapter->getDriver()->getConnection()->disconnect();

        self::assertFalse($this->adapter->getDriver()->getConnection()->isConnected());
        if ($isTcpConnection) {
            self::assertFalse($this->isConnectedTcp());
        }

        $this->adapter->getDriver()->getConnection()->connect();

        self::assertTrue($this->adapter->getDriver()->getConnection()->isConnected());
        if ($isTcpConnection) {
            self::assertTrue($this->isConnectedTcp());
        }

        $this->adapter->getPlatform()->quoteValue('test');

        $this->adapter->getDriver()->getConnection()->disconnect();

        self::assertFalse($this->adapter->getDriver()->getConnection()->isConnected());
        if ($isTcpConnection) {
            self::assertFalse($this->isConnectedTcp());
        }
    }

    protected function isConnectedTcp()
    {
        $mypid  = getmypid();
        $dbPort = static::DB_SERVER_PORT;
        $lsof   = shell_exec("lsof -i -P -n | grep $dbPort | grep $mypid");

        return $lsof !== null;
    }

    protected function isTcpConnection()
    {
        return $this->getHostname() !== 'localhost';
    }

    abstract protected function getHostname();
}
