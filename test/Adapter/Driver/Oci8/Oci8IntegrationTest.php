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
     * @covers Laminas\Db\Adapter\Driver\Oci8\Oci8::checkEnvironment
     */
    public function testCheckEnvironment()
    {
        $sqlserver = new Oci8(array());
        $this->assertNull($sqlserver->checkEnvironment());
    }

    public function testCreateStatement()
    {
        $driver = new Oci8(array());
        $resource = oci_connect($this->variables['username'], $this->variables['password']);

        $driver->getConnection()->setResource($resource);

        $stmt = $driver->createStatement('SELECT * FROM DUAL');
        $this->assertInstanceOf('Laminas\Db\Adapter\Driver\Oci8\Statement', $stmt);
        $stmt = $driver->createStatement();
        $this->assertInstanceOf('Laminas\Db\Adapter\Driver\Oci8\Statement', $stmt);

        $this->setExpectedException('Laminas\Db\Adapter\Exception\InvalidArgumentException', 'only accepts an SQL string or a oci8 resource');
        $driver->createStatement(new \stdClass);
    }

}
