<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pgsql;

use Laminas\Db\Adapter\Driver\Pgsql\Connection;
use Laminas\Db\Adapter\Exception as AdapterException;
use ReflectionMethod;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $connection = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->connection = new Connection();
    }

    /**
     * Test getResource method if it tries to connect to the database.
     *
     * @covers Laminas\Db\Adapter\Driver\Pgsql\Connection::getResource
     */
    public function testResource()
    {
        if (! extension_loaded('pgsql')) {
            $this->markTestSkipped('pgsql extension not loaded');
        }

        try {
            $resource = $this->connection->getResource();
            // connected with empty string
            $this->assertInternalType('resource', $resource);
        } catch (AdapterException\RuntimeException $exc) {
            // If it throws an exception it has failed to connect
            $this->setExpectedException('Laminas\Db\Adapter\Exception\RuntimeException');
            throw $exc;
        }
    }

    /**
     * Test disconnect method to return instance of ConnectionInterface
     */
    public function testDisconnect()
    {
        include_once 'pgsqlMockFunctions.php';
        $this->assertSame($this->connection, $this->connection->disconnect());
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

        $this->assertEquals(
            'host=localhost user=test password=test123! dbname=test',
            $getConnectionString->invoke($this->connection)
        );
    }

    /**
     * @expectedException \Laminas\Db\Adapter\Exception\InvalidArgumentException
     */
    public function testSetConnectionTypeException()
    {
        if (! extension_loaded('pgsql')) {
            $this->markTestSkipped('pgsql extension not loaded');
        }
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
        $this->assertEquals($type, self::readAttribute($this->connection, 'type'));
    }
}
