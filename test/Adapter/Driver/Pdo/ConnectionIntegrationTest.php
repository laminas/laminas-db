<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Driver\Pdo\Connection;
use Laminas\Db\Adapter\Driver\Pdo\Pdo;

/**
 * @group integration
 * @group integration-pdo
 */
class ConnectionIntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected $variables = array('pdodriver' => 'sqlite', 'database' => ':memory:');

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::getCurrentSchema
     */
    public function testGetCurrentSchema()
    {
        $connection = new Connection($this->variables);
        $this->assertInternalType('string', $connection->getCurrentSchema());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::setResource
     */
    public function testSetResource()
    {
        $resource = new TestAsset\SqliteMemoryPdo();
        $connection = new Connection(array());
        $this->assertSame($connection, $connection->setResource($resource));

        $connection->disconnect();
        unset($connection);
        unset($resource);
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::getResource
     */
    public function testGetResource()
    {
        $connection = new Connection($this->variables);
        $connection->connect();
        $this->assertInstanceOf('PDO', $connection->getResource());

        $connection->disconnect();
        unset($connection);
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::connect
     */
    public function testConnect()
    {
        $connection = new Connection($this->variables);
        $this->assertSame($connection, $connection->connect());
        $this->assertTrue($connection->isConnected());

        $connection->disconnect();
        unset($connection);
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::isConnected
     */
    public function testIsConnected()
    {
        $connection = new Connection($this->variables);
        $this->assertFalse($connection->isConnected());
        $this->assertSame($connection, $connection->connect());
        $this->assertTrue($connection->isConnected());

        $connection->disconnect();
        unset($connection);
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::disconnect
     */
    public function testDisconnect()
    {
        $connection = new Connection($this->variables);
        $connection->connect();
        $this->assertTrue($connection->isConnected());
        $connection->disconnect();
        $this->assertFalse($connection->isConnected());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::beginTransaction
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
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::commit
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
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::rollback
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
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::execute
     */
    public function testExecute()
    {
        $sqlsrv = new Pdo($this->variables);
        $connection = $sqlsrv->getConnection();

        $result = $connection->execute('SELECT \'foo\'');
        $this->assertInstanceOf('Laminas\Db\Adapter\Driver\Pdo\Result', $result);
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::prepare
     */
    public function testPrepare()
    {
        $sqlsrv = new Pdo($this->variables);
        $connection = $sqlsrv->getConnection();

        $statement = $connection->prepare('SELECT \'foo\'');
        $this->assertInstanceOf('Laminas\Db\Adapter\Driver\Pdo\Statement', $statement);
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::getLastGeneratedValue
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
        $resource = new TestAsset\SqliteMemoryPdo();
        $connection = new Connection(array());
        $connection->setResource($resource);
        $this->assertSame($connection, $connection->connect());

        $connection->disconnect();
        unset($connection);
        unset($resource);
    }

}
