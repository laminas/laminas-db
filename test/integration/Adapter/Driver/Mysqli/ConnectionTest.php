<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Mysqli;

use Laminas\Db\Adapter\Driver\Mysqli\Connection;
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
        $connection = new Connection($this->variables);
        $connection->connect();

        self::assertTrue($connection->isConnected());
        $connection->disconnect();
    }
}
