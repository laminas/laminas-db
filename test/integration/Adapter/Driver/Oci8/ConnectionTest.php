<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use PHPUnit\Framework\TestCase;

/**
 * @group integration
 * @group integration-oracle
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
}
