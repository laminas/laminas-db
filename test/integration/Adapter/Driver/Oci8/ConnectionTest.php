<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 * @group integration-mysqli
 */
class ConnectionTest extends TestCase
{
    use TraitSetup;

    public function testConnectionOk()
    {
        $connection = $this->createConnection();
        $connection->connect();

        $result = $connection->isConnected();
        self::assertTrue($result);

        $connection->disconnect();
    }

//    public function testSelectWithEmptyCurrentWithoutBufferResult()
//    {
//        $adapter = $this->createAdapter();
//        $tableGateway = new TableGateway('test', $adapter);
//        $rowset = $tableGateway->select('id = 0');
//
//        $result = $rowset->current();
//        $this->assertNull($result);
//
//        $adapter->getDriver()->getConnection()->disconnect();
//    }
}
