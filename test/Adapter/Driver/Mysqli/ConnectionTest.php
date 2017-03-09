<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Adapter\Driver\Mysqli;

use Zend\Db\Adapter\Driver\Mysqli\Connection;
use Zend\Db\Adapter\Driver\Mysqli\Mysqli;

class ConnectionTest extends \PHPUnit_Framework_TestCase
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
     * @covers Zend\Db\Adapter\Driver\Mysqli\Connection::setDriver
     */
    public function testSetDriver()
    {
        $this->assertEquals($this->connection, $this->connection->setDriver(new Mysqli([])));
    }

    /**
     * @covers Zend\Db\Adapter\Driver\Mysqli\Connection::setConnectionParameters
     */
    public function testSetConnectionParameters()
    {
        $this->assertEquals($this->connection, $this->connection->setConnectionParameters([]));
    }

    /**
     * @covers Zend\Db\Adapter\Driver\Mysqli\Connection::getConnectionParameters
     */
    public function testGetConnectionParameters()
    {
        $this->connection->setConnectionParameters(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $this->connection->getConnectionParameters());
    }

    /**
     * @expectedException \Zend\Db\Adapter\Exception\RuntimeException
     * @expectedExceptionMessage Connection error
     */
    public function testConnectionFails()
    {
        $connection = new Connection([]);
        $connection->connect();
    }
}
