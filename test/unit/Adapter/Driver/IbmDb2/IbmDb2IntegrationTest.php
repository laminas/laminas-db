<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\IbmDb2;

use Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2;

/**
 * @group integration
 * @group integration-ibm_db2
 */
class IbmDb2IntegrationTest extends AbstractIntegrationTest
{
    /**
     * @group integration-ibm_db2
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::checkEnvironment
     */
    public function testCheckEnvironment()
    {
        $ibmdb2 = new IbmDb2([]);
        self::assertNull($ibmdb2->checkEnvironment());
    }

    public function testCreateStatement()
    {
        $driver = new IbmDb2([]);

        $resource = db2_connect(
            $this->variables['database'],
            $this->variables['username'],
            $this->variables['password']
        );

        $stmtResource = db2_prepare($resource, 'SELECT 1 FROM SYSIBM.SYSDUMMY1');

        $driver->getConnection()->setResource($resource);

        $stmt = $driver->createStatement('SELECT 1 FROM SYSIBM.SYSDUMMY1');
        self::assertInstanceOf('Laminas\Db\Adapter\Driver\IbmDb2\Statement', $stmt);
        $stmt = $driver->createStatement($stmtResource);
        self::assertInstanceOf('Laminas\Db\Adapter\Driver\IbmDb2\Statement', $stmt);
        $stmt = $driver->createStatement();
        self::assertInstanceOf('Laminas\Db\Adapter\Driver\IbmDb2\Statement', $stmt);

        $this->expectException('Laminas\Db\Adapter\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('only accepts an SQL string or an ibm_db2 resource');
        $driver->createStatement(new \stdClass);
    }
}
