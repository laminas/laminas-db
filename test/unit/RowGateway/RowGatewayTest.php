<?php

namespace LaminasTest\Db\RowGateway;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ConnectionInterface;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\RowGateway\Exception\RuntimeException;
use Laminas\Db\RowGateway\RowGateway;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RowGatewayTest extends TestCase
{
    /** @var Adapter&MockObject */
    protected $mockAdapter;

    /** @var RowGateway */
    protected $rowGateway;

    /** @var ResultInterface&MockObject */
    protected $mockResult;
    protected function setUp(): void
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMockBuilder(ResultInterface::class)->getMock();
        $mockResult->expects($this->any())->method('getAffectedRows')->will($this->returnValue(1));
        $this->mockResult = $mockResult;

        $mockStatement = $this->getMockBuilder(StatementInterface::class)->getMock();
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));

        $mockConnection = $this->getMockBuilder(ConnectionInterface::class)->getMock();

        $mockDriver = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        // setup mock adapter
        $this->mockAdapter = $this->getMockBuilder(Adapter::class)
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();
    }

    public function testEmptyPrimaryKey()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This row object does not have a primary key column set.');
        $this->rowGateway = new RowGateway('', 'foo', $this->mockAdapter);
    }
}
