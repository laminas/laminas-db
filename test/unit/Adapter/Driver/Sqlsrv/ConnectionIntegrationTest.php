<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Sqlsrv;

use Laminas\Db\Adapter\Driver\Sqlsrv\Connection;
use Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv;

/**
 * @group integration
 * @group integration-sqlserver
 */
class ConnectionIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::getCurrentSchema
     */
    public function testGetCurrentSchema()
    {
        $connection = new Connection($this->variables);
        self::assertInternalType('string', $connection->getCurrentSchema());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::setResource
     */
    public function testSetResource()
    {
        $resource = sqlsrv_connect(
            $this->variables['hostname'],
            [
                'UID' => $this->variables['username'],
                'PWD' => $this->variables['password'],
            ]
        );
        $connection = new Connection([]);
        self::assertSame($connection, $connection->setResource($resource));

        $connection->disconnect();
        unset($connection);
        unset($resource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::getResource
     */
    public function testGetResource()
    {
        $connection = new Connection($this->variables);
        $connection->connect();
        self::assertInternalType('resource', $connection->getResource());

        $connection->disconnect();
        unset($connection);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::connect
     */
    public function testConnect()
    {
        $connection = new Connection($this->variables);
        self::assertSame($connection, $connection->connect());
        self::assertTrue($connection->isConnected());

        $connection->disconnect();
        unset($connection);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::isConnected
     */
    public function testIsConnected()
    {
        $connection = new Connection($this->variables);
        self::assertFalse($connection->isConnected());
        self::assertSame($connection, $connection->connect());
        self::assertTrue($connection->isConnected());

        $connection->disconnect();
        unset($connection);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::disconnect
     */
    public function testDisconnect()
    {
        $connection = new Connection($this->variables);
        $connection->connect();
        self::assertTrue($connection->isConnected());
        $connection->disconnect();
        self::assertFalse($connection->isConnected());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::beginTransaction
     * @todo   Implement testBeginTransaction().
     */
    public function testBeginTransaction()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::commit
     * @todo   Implement testCommit().
     */
    public function testCommit()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::rollback
     * @todo   Implement testRollback().
     */
    public function testRollback()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::execute
     */
    public function testExecute()
    {
        $sqlsrv = new Sqlsrv($this->variables);
        $connection = $sqlsrv->getConnection();

        $result = $connection->execute('SELECT \'foo\'');
        self::assertInstanceOf('Laminas\Db\Adapter\Driver\Sqlsrv\Result', $result);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::prepare
     */
    public function testPrepare()
    {
        $sqlsrv = new Sqlsrv($this->variables);
        $connection = $sqlsrv->getConnection();

        $statement = $connection->prepare('SELECT \'foo\'');
        self::assertInstanceOf('Laminas\Db\Adapter\Driver\Sqlsrv\Statement', $statement);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Connection::getLastGeneratedValue
     */
    public function testGetLastGeneratedValue()
    {
        $this->markTestIncomplete('Need to create a temporary sequence.');
        $connection = new Connection($this->variables);
        $connection->getLastGeneratedValue();
    }

    /**
     * @group laminas3469
     */
    public function testConnectReturnsConnectionWhenResourceSet()
    {
        $resource = sqlsrv_connect(
            $this->variables['hostname'],
            [
                'UID' => $this->variables['username'],
                'PWD' => $this->variables['password'],
            ]
        );
        $connection = new Connection([]);
        $connection->setResource($resource);
        self::assertSame($connection, $connection->connect());

        $connection->disconnect();
        unset($connection);
        unset($resource);
    }
}
