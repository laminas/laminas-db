<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Driver\Oci8\Oci8;

/**
 * @group integration
 * @group integration-oracle
 */
class Oci8IntegrationTest extends AbstractIntegrationTest
{
    /**
     * @group integration-oci8
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::checkEnvironment
     */
    public function testCheckEnvironment()
    {
        $sqlserver = new Oci8([]);
        self::assertNull($sqlserver->checkEnvironment());
    }

    public function testCreateStatement()
    {
        $driver = new Oci8([]);
        $resource = oci_connect(
            $this->variables['username'],
            $this->variables['password'],
            $this->variables['hostname']
        );

        $driver->getConnection()->setResource($resource);

        $stmt = $driver->createStatement('SELECT * FROM DUAL');
        self::assertInstanceOf('Laminas\Db\Adapter\Driver\Oci8\Statement', $stmt);
        $stmt = $driver->createStatement();
        self::assertInstanceOf('Laminas\Db\Adapter\Driver\Oci8\Statement', $stmt);

        $this->expectException('Laminas\Db\Adapter\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('only accepts an SQL string or an oci8 resource');
        $driver->createStatement(new \stdClass);
    }
}
