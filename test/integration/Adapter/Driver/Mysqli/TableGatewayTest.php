<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Mysqli;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

class TableGatewayTest extends TestCase
{
    use TraitSetup;

    /**
     * @see https://github.com/zendframework/zend-db/issues/330
     */
    public function testSelectWithEmptyCurrentWithBufferResult()
    {
        $adapter      = new Adapter([
            'driver'   => 'mysqli',
            'database' => $this->variables['database'],
            'hostname' => $this->variables['hostname'],
            'username' => $this->variables['username'],
            'password' => $this->variables['password'],
            'options'  => ['buffer_results' => true],
        ]);
        $tableGateway = new TableGateway('test', $adapter);
        $rowset       = $tableGateway->select('id = 0');

        $this->assertNull($rowset->current());

        $adapter->getDriver()->getConnection()->disconnect();
    }

    /**
     * @see https://github.com/zendframework/zend-db/issues/330
     */
    public function testSelectWithEmptyCurrentWithoutBufferResult()
    {
        $adapter      = new Adapter([
            'driver'   => 'mysqli',
            'database' => $this->variables['database'],
            'hostname' => $this->variables['hostname'],
            'username' => $this->variables['username'],
            'password' => $this->variables['password'],
            'options'  => ['buffer_results' => false],
        ]);
        $tableGateway = new TableGateway('test', $adapter);
        $rowset       = $tableGateway->select('id = 0');

        $this->assertNull($rowset->current());

        $adapter->getDriver()->getConnection()->disconnect();
    }
}
