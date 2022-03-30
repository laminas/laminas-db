<?php

namespace LaminasTest\Db\Adapter\Driver\Sqlsrv;

use Laminas\Db\Adapter\Driver\Pdo\Pdo;

/**
 * @group integration
 * @group integration-sqlserver
 */
class PdoSqlSrvIntegrationTest extends AbstractIntegrationTest
{
    public function testParameterizedQuery()
    {
        if (! isset($this->adapters['pdo_sqlsrv'])) {
            $this->markTestSkipped('pdo_sqlsrv adapter is not found');
        }

        $driver = new Pdo($this->adapters['pdo_sqlsrv']);

        $stmt   = $driver->createStatement('SELECT ? as col_one');
        $result = $stmt->execute(['a']);
        $row    = $result->current();
        $this->assertEquals('a', $row['col_one']);
    }
}
