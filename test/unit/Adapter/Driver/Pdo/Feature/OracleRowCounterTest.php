<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pdo\Feature;

use Closure;
use Laminas\Db\Adapter\Driver\ConnectionInterface;
use Laminas\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter;
use Laminas\Db\Adapter\Driver\Pdo\Pdo;
use Laminas\Db\Adapter\Driver\Pdo\Statement;
use Laminas\Db\Adapter\Driver\ResultInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class OracleRowCounterTest extends TestCase
{
    /** @var OracleRowCounter */
    protected $rowCounter;

    protected function setUp(): void
    {
        $this->rowCounter = new OracleRowCounter();
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter::getName
     */
    public function testGetName()
    {
        self::assertEquals('OracleRowCounter', $this->rowCounter->getName());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter::getCountForStatement
     */
    public function testGetCountForStatement()
    {
        $statement = $this->getMockStatement('SELECT XXX', 5);
        $statement->expects($this->once())->method('prepare')
            ->with($this->equalTo('SELECT COUNT(*) as "count" FROM (SELECT XXX)'));

        $count = $this->rowCounter->getCountForStatement($statement);
        self::assertEquals(5, $count);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter::getCountForSql
     */
    public function testGetCountForSql()
    {
        $this->rowCounter->setDriver($this->getMockDriver(5));
        $count = $this->rowCounter->getCountForSql('SELECT XXX');
        self::assertEquals(5, $count);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter::getRowCountClosure
     */
    public function testGetRowCountClosure()
    {
        $stmt = $this->getMockStatement('SELECT XXX', 5);

        /** @var Closure $closure */
        $closure = $this->rowCounter->getRowCountClosure($stmt);
        self::assertInstanceOf('Closure', $closure);
        self::assertEquals(5, $closure());
    }

    protected function getMockStatement($sql, $returnValue)
    {
        /** @var Statement|PHPUnit_Framework_MockObject_MockObject $statement */
        $statement = $this->getMockBuilder(Statement::class)
            ->setMethods(['prepare', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        // mock PDOStatement with stdClass
        $resource = $this->getMockBuilder('stdClass')
            ->setMethods(['fetch'])
            ->getMock();
        $resource->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue(['count' => $returnValue]));

        // mock the result
        $result = $this->getMockBuilder(ResultInterface::class)->getMock();
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
        $pdoStatement = $this->getMockBuilder('stdClass')
            ->setMethods(['fetch'])
            ->disableOriginalConstructor()
            ->getMock(); // stdClass can be used here
        $pdoStatement->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue(['count' => $returnValue]));

        $pdoConnection = $this->getMockBuilder('stdClass')
            ->setMethods(['query'])
            ->getMock();
        $pdoConnection->expects($this->once())
            ->method('query')
            ->will($this->returnValue($pdoStatement));

        $connection = $this->getMockBuilder(ConnectionInterface::class)->getMock();
        $connection->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($pdoConnection));

        $driver = $this->getMockBuilder(Pdo::class)
            ->setMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $driver->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        return $driver;
    }
}
