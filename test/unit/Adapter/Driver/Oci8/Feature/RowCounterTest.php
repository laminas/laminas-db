<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Adapter\Driver\Oci8\Feature;

use PHPUnit\Framework\TestCase;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\Oci8\Feature\RowCounter;
use Zend\Db\Adapter\Driver\Oci8\Statement as Oci8Statement;

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
     * @covers \Zend\Db\Adapter\Driver\Oci8\Feature\RowCounter::getName
     */
    public function testGetName()
    {
        self::assertEquals('RowCounter', $this->rowCounter->getName());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Oci8\Feature\RowCounter::getCountForStatement
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
     * @covers \Zend\Db\Adapter\Driver\Oci8\Feature\RowCounter::getCountForSql
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

        $result = $this->createMock('Zend\Db\Adapter\Driver\ResultInterface');
        $result->expects($this->once())
            ->method('current')
            ->will($this->returnValue(['count' => $returnValue]));

        $connection = $this->getMockBuilder('Zend\Db\Adapter\Driver\ConnectionInterface')->getMock();
        $connection->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));
        $driver = $this->getMockBuilder('Zend\Db\Adapter\Driver\Oci8\Oci8')
            ->setMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $driver->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        return $driver;
    }
}
