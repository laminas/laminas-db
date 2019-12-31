<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\RowGateway;

use Laminas\Db\RowGateway\RowGateway;
use PHPUnit_Framework_TestCase;

class RowGatewayTest extends PHPUnit_Framework_TestCase
{
    protected $mockAdapter;
    protected $rowGateway;

    public function setup()
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMock('Laminas\Db\Adapter\Driver\ResultInterface');
        $mockResult->expects($this->any())->method('getAffectedRows')->will($this->returnValue(1));
        $this->mockResult = $mockResult;

        $mockStatement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));

        $mockConnection = $this->getMock('Laminas\Db\Adapter\Driver\ConnectionInterface');

        $mockDriver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        // setup mock adapter
        $this->mockAdapter = $this->getMock('Laminas\Db\Adapter\Adapter', null, [$mockDriver]);
    }

    public function testEmptyPrimaryKey()
    {
        $this->setExpectedException('Laminas\Db\RowGateway\Exception\RuntimeException', 'This row object does not have a primary key column set.');
        $this->rowGateway = new RowGateway('', 'foo', $this->mockAdapter);
    }
}
