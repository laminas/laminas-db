<?php

namespace LaminasTest\Db\Adapter\Driver\Oci8\Feature;

use Closure;
use Laminas\Db\Adapter\Driver\ConnectionInterface;
use Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter;
use Laminas\Db\Adapter\Driver\Oci8\Oci8;
use Laminas\Db\Adapter\Driver\Oci8\Statement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RowCounterTest extends TestCase
{
    /** @var RowCounter */
    protected $rowCounter;

    protected function setUp(): void
    {
        $this->rowCounter = new RowCounter();
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter::getName
     */
    public function testGetName()
    {
        self::assertEquals('RowCounter', $this->rowCounter->getName());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter::getCountForStatement
     */
    public function testGetCountForStatement()
    {
        $statement = $this->getMockStatement('SELECT XXX', 5);
        $statement->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('SELECT COUNT(*) as "count" FROM (SELECT XXX)'));
        $count = $this->rowCounter->getCountForStatement($statement);
        self::assertEquals(5, $count);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter::getCountForSql
     */
    public function testGetCountForSql()
    {
        $this->rowCounter->setDriver($this->getMockDriver(5));
        $count = $this->rowCounter->getCountForSql('SELECT XXX');
        self::assertEquals(5, $count);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter::getRowCountClosure
     */
    public function testGetRowCountClosure()
    {
        $stmt = $this->getMockStatement('SELECT XXX', 5);
        /** @var Closure $closure */
        $closure = $this->rowCounter->getRowCountClosure($stmt);
        self::assertInstanceOf('Closure', $closure);
        self::assertEquals(5, $closure());
    }

    /**
     * @param string $sql
     * @param mixed $returnValue
     * @return Statement&MockObject
     */
    protected function getMockStatement($sql, $returnValue)
    {
        $statement = $this->getMockBuilder(Statement::class)
            ->setMethods(['prepare', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        // mock the result
        $result = $this->getMockBuilder('stdClass')
            ->setMethods(['current'])
            ->getMock();
        $result->expects($this->once())
            ->method('current')
            ->will($this->returnValue(['count' => $returnValue]));
        $statement->setSql($sql);
        $statement->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));
        return $statement;
    }

    /**
     * @param mixed $returnValue
     * @return Oci8&MockObject
     */
    protected function getMockDriver($returnValue)
    {
        $oci8Statement = $this->getMockBuilder('stdClass')
            ->setMethods(['current'])
            ->disableOriginalConstructor()
            ->getMock(); // stdClass can be used here
        $oci8Statement->expects($this->once())
            ->method('current')
            ->will($this->returnValue(['count' => $returnValue]));
        $connection = $this->getMockBuilder(ConnectionInterface::class)->getMock();
        $connection->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($oci8Statement));
        $driver = $this->getMockBuilder(Oci8::class)
            ->setMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $driver->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        return $driver;
    }
}
