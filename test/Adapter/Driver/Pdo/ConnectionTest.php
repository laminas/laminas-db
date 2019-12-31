<?php
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
}
