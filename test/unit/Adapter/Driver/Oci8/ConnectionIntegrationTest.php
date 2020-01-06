<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Driver\Oci8\Connection;
use Laminas\Db\Adapter\Driver\Oci8\Oci8;

/**
 * @group integration
 * @group integration-oracle
 */
class ConnectionIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::getCurrentSchema
     */
    public function testGetCurrentSchema()
    {
        $connection = new Connection($this->variables);
        self::assertInternalType('string', $connection->getCurrentSchema());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::setResource
     */
    public function testSetResource()
    {
        $this->markTestIncomplete('edit this');
        $resource = oci_connect(
            $this->variables['username'],
            $this->variables['password'],
            $this->variables['hostname']
        );

        $connection = new Connection([]);
        self::assertSame($connection, $connection->setResource($resource));

        $connection->disconnect();
        unset($connection);
        unset($resource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::getResource
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::connect
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::isConnected
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::disconnect
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::beginTransaction
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::commit
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::rollback
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::execute
     */
    public function testExecute()
    {
        $oci8 = new Oci8($this->variables);
        $connection = $oci8->getConnection();

        $result = $connection->execute('SELECT \'foo\' FROM DUAL');
        self::assertInstanceOf('Laminas\Db\Adapter\Driver\Oci8\Result', $result);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Connection::getLastGeneratedValue
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
        $this->markTestIncomplete('edit this');
        $resource = oci_connect(
            $this->variables['username'],
            $this->variables['password'],
            $this->variables['hostname']
        );

        $connection = new Connection([]);
        $connection->setResource($resource);
        self::assertSame($connection, $connection->connect());

        $connection->disconnect();
        unset($connection);
        unset($resource);
    }
}
