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
            $this->assertTrue(is_resource($resource));
        } catch (AdapterException\RuntimeException $exc) {
            // If it throws an exception it has failed to connect
            $this->setExpectedException('Laminas\Db\Adapter\Exception\RuntimeException');
            throw $exc;
        }
    }

    /**
     * @group 6760
     * @group 6787
     */
    public function testGetConnectionStringEncodeSpecialSymbol()
    {
        $connectionParameters = array(
            'driver'    => 'pgsql',
            'host' => 'localhost',
            'post' => '5432',
            'dbname' => 'test',
            'username'  => 'test',
            'password'  => 'test123!',
        );

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
}
