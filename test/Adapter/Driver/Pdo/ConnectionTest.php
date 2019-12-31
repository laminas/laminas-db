<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Driver\Pdo\Connection;

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
     * Test getResource method tries to connect to  the database, it should never return null
     *
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::getResource
     */
    public function testResource()
    {
        $this->setExpectedException('Laminas\Db\Adapter\Exception\InvalidConnectionParametersException');
        $this->connection->getResource();
    }

    /**
     * Test getConnectedDsn returns a DSN string if it has been set
     *
     * @covers Laminas\Db\Adapter\Driver\Pdo\Connection::getDsn
     */
    public function testGetDsn()
    {
        $dsn = "sqlite::memory:";
        $this->connection->setConnectionParameters(['dsn' => $dsn]);
        try {
            $this->connection->connect();
        } catch (\Exception $e) {
        }
        $responseString = $this->connection->getDsn();

        $this->assertEquals($dsn, $responseString);
    }

    /**
     * @group 2622
     */
    public function testArrayOfConnectionParametersCreatesCorrectDsn()
    {
        $this->connection->setConnectionParameters([
            'driver'  => 'pdo_mysql',
            'charset' => 'utf8',
            'dbname'  => 'foo',
            'port'    => '3306',
            'unix_socket' => '/var/run/mysqld/mysqld.sock',
        ]);
        try {
            $this->connection->connect();
        } catch (\Exception $e) {
        }
        $responseString = $this->connection->getDsn();

        $this->assertStringStartsWith('mysql:', $responseString);
        $this->assertContains('charset=utf8', $responseString);
        $this->assertContains('dbname=foo', $responseString);
        $this->assertContains('port=3306', $responseString);
        $this->assertContains('unix_socket=/var/run/mysqld/mysqld.sock', $responseString);
    }

    public function testHostnameAndUnixSocketThrowsInvalidConnectionParametersException()
    {
        $this->setExpectedException(
            'Laminas\Db\Adapter\Exception\InvalidConnectionParametersException',
            'Ambiguous connection parameters, both hostname and unix_socket parameters were set'
        );

        $this->connection->setConnectionParameters([
            'driver'  => 'pdo_mysql',
            'host'    => '127.0.0.1',
            'dbname'  => 'foo',
            'port'    => '3306',
            'unix_socket' => '/var/run/mysqld/mysqld.sock',
        ]);
        $this->connection->connect();
    }
}
