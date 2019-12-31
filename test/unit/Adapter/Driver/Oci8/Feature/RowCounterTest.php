<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Oci8\Feature;

use Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter;
use Laminas\Db\Adapter\Driver\Oci8\Statement as Oci8Statement;
use Laminas\Db\Adapter\Driver\ResultInterface;
use PHPUnit\Framework\TestCase;

class RowCounterTest extends TestCase
{
    /**
     * @var RowCounter
     */
    protected $rowCounter;

    protected function setUp()
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

    protected function getMockStatement($sql, $returnValue)
    {
        $statement = $this->getMockBuilder(Oci8Statement::class)
            ->setMethods(['prepare', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $result = $this->createMock(ResultInterface::class);
        $result->expects($this->once())
            ->method('current')
            ->will($this->returnValue(['count' => $returnValue]));
        $statement->setSql($sql);
        $statement->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));
        return $statement;
    }

    protected function getMockDriver($returnValue)
    {
        $oci8Statement = $this->getMockBuilder('stdClass')
            ->setMethods(['current'])
            ->disableOriginalConstructor()
            ->getMock(); // stdClass can be used here
        $oci8Statement->expects($this->once())
            ->method('current')
            ->will($this->returnValue(['count' => $returnValue]));

        $result = $this->createMock('Laminas\Db\Adapter\Driver\ResultInterface');
        $result->expects($this->once())
            ->method('current')
            ->will($this->returnValue(['count' => $returnValue]));

        $connection = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ConnectionInterface')->getMock();
        $connection->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));
        $driver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\Oci8\Oci8')
            ->setMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $driver->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        return $driver;
    }
}
