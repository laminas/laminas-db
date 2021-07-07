<?php

namespace LaminasTest\Db\Adapter\Driver\Sqlsrv;

use Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv;

/**
 * @group integration
 * @group integration-sqlserver
 */
class SqlSrvIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @var Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv
     */
    private $driver;

    /**
     * @var resource SQL Server Connection
     */
    private $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->adapters['sqlsrv'];
        $this->driver = new Sqlsrv($this->resource);
    }

    /**
     * @group integration-sqlserver
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::checkEnvironment
     */
    public function testCheckEnvironment()
    {
        $sqlserver = new Sqlsrv([]);
        self::assertNull($sqlserver->checkEnvironment());
    }

    public function testCreateStatement()
    {
        $stmt = $this->driver->createStatement('SELECT 1');
        $this->assertInstanceOf('Laminas\Db\Adapter\Driver\Sqlsrv\Statement', $stmt);
        $stmt = $this->driver->createStatement($this->resource);
        $this->assertInstanceOf('Laminas\Db\Adapter\Driver\Sqlsrv\Statement', $stmt);
        $stmt = $this->driver->createStatement();
        $this->assertInstanceOf('Laminas\Db\Adapter\Driver\Sqlsrv\Statement', $stmt);

        $this->expectException('Laminas\Db\Adapter\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('only accepts an SQL string or a Sqlsrv resource');
        $this->driver->createStatement(new \stdClass);
    }

    public function testParameterizedQuery()
    {
        $stmt = $this->driver->createStatement('SELECT ? as col_one');
        $result = $stmt->execute(['a']);
        $row = $result->current();
        $this->assertEquals('a', $row['col_one']);
    }
}
