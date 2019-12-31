<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pdo\Feature;

use Laminas\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter;
use Laminas\Db\Adapter\Driver\Pdo\Statement;
use PHPUnit_Framework_TestCase;

class OracleRowCounterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OracleRowCounter
     */
    protected $rowCounter;

    public function setUp()
    {
        $this->rowCounter = new OracleRowCounter();
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter::getName
     */
    public function testGetName()
    {
        $this->assertEquals('OracleRowCounter', $this->rowCounter->getName());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter::getCountForStatement
     */
    public function testGetCountForStatement()
    {
        $statement = $this->getMockStatement('SELECT XXX', 5);
        $statement->expects($this->once())->method('prepare')->with($this->equalTo('SELECT COUNT(*) as "count" FROM (SELECT XXX)'));

        $count = $this->rowCounter->getCountForStatement($statement);
        $this->assertEquals(5, $count);
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter::getCountForSql
     */
    public function testGetCountForSql()
    {
        $this->rowCounter->setDriver($this->getMockDriver(5));
        $count = $this->rowCounter->getCountForSql('SELECT XXX');
        $this->assertEquals(5, $count);
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter::getRowCountClosure
     */
    public function testGetRowCountClosure()
    {
        $stmt = $this->getMockStatement('SELECT XXX', 5);

        /** @var \Closure $closure */
        $closure = $this->rowCounter->getRowCountClosure($stmt);
        $this->assertInstanceOf('Closure', $closure);
        $this->assertEquals(5, $closure());
    }

    protected function getMockStatement($sql, $returnValue)
    {
        /** @var \Laminas\Db\Adapter\Driver\Pdo\Statement|\PHPUnit_Framework_MockObject_MockObject $statement */
        $statement = $this->getMock('Laminas\Db\Adapter\Driver\Pdo\Statement', array('prepare', 'execute'), array(), '', false);

        // mock PDOStatement with stdClass
        $resource = $this->getMock('stdClass', array('fetch'));
        $resource->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue(array('count' => $returnValue)));

        // mock the result
        $result = $this->getMock('Laminas\Db\Adapter\Driver\ResultInterface');
        $result->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource));

        $statement->setSql($sql);
        $statement->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));

        return $statement;
    }

    protected function getMockDriver($returnValue)
    {
        $pdoStatement = $this->getMock('stdClass', array('fetch'), array(), '', false); // stdClass can be used here
        $pdoStatement->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue(array('count' => $returnValue)));

        $pdoConnection = $this->getMock('stdClass', array('query'));
        $pdoConnection->expects($this->once())
            ->method('query')
            ->will($this->returnValue($pdoStatement));

        $connection = $this->getMock('Laminas\Db\Adapter\Driver\ConnectionInterface');
        $connection->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($pdoConnection));

        $driver = $this->getMock('Laminas\Db\Adapter\Driver\Pdo\Pdo', array('getConnection'), array(), '', false);
        $driver->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        return $driver;
    }
}
