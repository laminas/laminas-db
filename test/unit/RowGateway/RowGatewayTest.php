<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\RowGateway;

use Laminas\Db\RowGateway\RowGateway;
use PHPUnit\Framework\TestCase;

class RowGatewayTest extends TestCase
{
    protected $mockAdapter;
    protected $rowGateway;

    protected function setUp()
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ResultInterface')->getMock();
        $mockResult->expects($this->any())->method('getAffectedRows')->will($this->returnValue(1));
        $this->mockResult = $mockResult;

        $mockStatement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));

        $mockConnection = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ConnectionInterface')->getMock();

        $mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        // setup mock adapter
        $this->mockAdapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();
    }

    public function testEmptyPrimaryKey()
    {
        $this->expectException('Laminas\Db\RowGateway\Exception\RuntimeException');
        $this->expectExceptionMessage('This row object does not have a primary key column set.');
        $this->rowGateway = new RowGateway('', 'foo', $this->mockAdapter);
    }
}
