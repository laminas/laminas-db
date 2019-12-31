<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Oci8\Feature;

use Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter;
use PHPUnit_Framework_TestCase;

class RowCounterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RowCounter
     */
    protected $rowCounter;

    public function setUp()
    {
        $this->rowCounter = new RowCounter();
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter::getName
     */
    public function testGetName()
    {
        $this->assertEquals('RowCounter', $this->rowCounter->getName());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter::getCountForStatement
     */
    public function testGetCountForStatement()
    {
        $statement = $this->getMockStatement('SELECT XXX', 5);
        $statement->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('SELECT COUNT(*) as "count" FROM (SELECT XXX)'));
        $count = $this->rowCounter->getCountForStatement($statement);
        $this->assertEquals(5, $count);
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter::getCountForSql
     */
    public function testGetCountForSql()
    {
        $this->rowCounter->setDriver($this->getMockDriver(5));
        $count = $this->rowCounter->getCountForSql('SELECT XXX');
        $this->assertEquals(5, $count);
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Feature\RowCounter::getRowCountClosure
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
        $statement = $this->getMock(
            'Laminas\Db\Adapter\Driver\Oci8\Statement',
            ['prepare', 'execute'],
            [],
            '',
            false
        );

        // mock the result
        $result = $this->getMock('stdClass', ['current']);
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
        $oci8Statement = $this->getMock('stdClass', ['current'], [], '', false); // stdClass can be used here
        $oci8Statement->expects($this->once())
            ->method('current')
            ->will($this->returnValue(['count' => $returnValue]));
        $connection = $this->getMock('Laminas\Db\Adapter\Driver\ConnectionInterface');
        $connection->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($oci8Statement));
        $driver = $this->getMock('Laminas\Db\Adapter\Driver\Oci8\Oci8', ['getConnection'], [], '', false);
        $driver->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        return $driver;
    }
}
