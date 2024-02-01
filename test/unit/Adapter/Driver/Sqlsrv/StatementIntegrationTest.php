<?php

namespace LaminasTest\Db\Adapter\Driver\Sqlsrv;

use Laminas\Db\Adapter\Driver\Sqlsrv\Result;
use Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv;
use Laminas\Db\Adapter\Driver\Sqlsrv\Statement;

use function get_resource_type;
use function sqlsrv_connect;

/**
 * @group integration
 * @group integration-sqlserver
 */
class StatementIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Statement::initialize
     */
    public function testInitialize()
    {
        $sqlsrvResource = sqlsrv_connect(
            $this->variables['hostname'],
            [
                'UID'                    => $this->variables['username'],
                'PWD'                    => $this->variables['password'],
                'TrustServerCertificate' => 1,
            ]
        );

        $statement = new Statement();
        self::assertSame($statement, $statement->initialize($sqlsrvResource));
        unset($stmtResource, $sqlsrvResource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Statement::getResource
     */
    public function testGetResource()
    {
        $sqlsrvResource = sqlsrv_connect(
            $this->variables['hostname'],
            [
                'UID'                    => $this->variables['username'],
                'PWD'                    => $this->variables['password'],
                'TrustServerCertificate' => 1,
            ]
        );

        $statement = new Statement();
        $statement->initialize($sqlsrvResource);
        $statement->prepare("SELECT 'foo'");
        $resource = $statement->getResource();
        self::assertEquals('SQL Server Statement', get_resource_type($resource));
        unset($resource, $sqlsrvResource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Statement::prepare
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Statement::isPrepared
     */
    public function testPrepare()
    {
        $sqlsrvResource = sqlsrv_connect(
            $this->variables['hostname'],
            [
                'UID'                    => $this->variables['username'],
                'PWD'                    => $this->variables['password'],
                'TrustServerCertificate' => 1,
            ]
        );

        $statement = new Statement();
        $statement->initialize($sqlsrvResource);
        self::assertFalse($statement->isPrepared());
        self::assertSame($statement, $statement->prepare("SELECT 'foo'"));
        self::assertTrue($statement->isPrepared());
        unset($resource, $sqlsrvResource);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Statement::execute
     */
    public function testExecute()
    {
        $sqlsrv    = new Sqlsrv($this->variables);
        $statement = $sqlsrv->createStatement("SELECT 'foo'");
        self::assertSame($statement, $statement->prepare());

        $result = $statement->execute();
        self::assertInstanceOf(Result::class, $result);

        unset($resource, $sqlsrvResource);
    }
}
