<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

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

    public function testSelectWithEmptyCurrentWithoutBufferResult()
    {
        $adapter = $this->createAdapter();
        try {
            $tableGateway = new TableGateway([
                't' => 'DUAL',
            ], $adapter);
            $rowset       = $tableGateway->select();

            $result   = $rowset->current();
            $actual   = $result->getArrayCopy();
            $expected = [
                'DUMMY' => 'X',
            ];
            $this->assertEquals($expected, $actual);
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }
}
