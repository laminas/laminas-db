<?php

namespace LaminasTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Driver\Oci8\Oci8;
use Laminas\Db\Adapter\Driver\Oci8\Statement;
use Laminas\Db\Adapter\Exception\InvalidArgumentException;
use stdClass;

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
        $driver   = new Oci8([]);
        $resource = oci_connect(
            $this->variables['username'],
            $this->variables['password'],
            $this->variables['hostname']
        );

        $driver->getConnection()->setResource($resource);

        $stmt = $driver->createStatement('SELECT * FROM DUAL');
        self::assertInstanceOf(Statement::class, $stmt);
        $stmt = $driver->createStatement();
        self::assertInstanceOf(Statement::class, $stmt);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('only accepts an SQL string or an oci8 resource');
        $driver->createStatement(new stdClass());
    }
}
