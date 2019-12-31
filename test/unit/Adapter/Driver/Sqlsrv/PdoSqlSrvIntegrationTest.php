<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

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
        $driver = new Pdo($this->adapters['pdo_sqlsrv']);

        $stmt = $driver->createStatement('SELECT ? as col_one');
        $result = $stmt->execute(['a']);
        $row = $result->current();
        $this->assertEquals('a', $row['col_one']);
    }
}
