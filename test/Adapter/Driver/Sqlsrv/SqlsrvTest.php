<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Sqlsrv;

use Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv;
use PHPUnit\Framework\TestCase;

class SqlsrvTest extends TestCase
{
    /**
     * @var Sqlsrv
     */
    protected $sqlsrv;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->sqlsrv = new Sqlsrv([]);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::registerConnection
     */
    public function testRegisterConnection()
    {
        $mockConnection = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\Sqlsrv\Connection',
            [[]],
            '',
            true,
            true,
            true,
            ['setDriver']
        );
        $mockConnection->expects($this->once())->method('setDriver')->with($this->equalTo($this->sqlsrv));
        self::assertSame($this->sqlsrv, $this->sqlsrv->registerConnection($mockConnection));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::registerStatementPrototype
     */
    public function testRegisterStatementPrototype()
    {
        $this->sqlsrv = new Sqlsrv([]);
        $mockStatement = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\Sqlsrv\Statement',
            [],
            '',
            true,
            true,
            true,
            ['setDriver']
        );
        $mockStatement->expects($this->once())->method('setDriver')->with($this->equalTo($this->sqlsrv));
        self::assertSame($this->sqlsrv, $this->sqlsrv->registerStatementPrototype($mockStatement));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::registerResultPrototype
     */
    public function testRegisterResultPrototype()
    {
        $this->sqlsrv = new Sqlsrv([]);
        $mockStatement = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\Sqlsrv\Result',
            [],
            '',
            true,
            true,
            true,
            ['setDriver']
        );
        self::assertSame($this->sqlsrv, $this->sqlsrv->registerResultPrototype($mockStatement));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::getDatabasePlatformName
     */
    public function testGetDatabasePlatformName()
    {
        $this->sqlsrv = new Sqlsrv([]);
        self::assertEquals('SqlServer', $this->sqlsrv->getDatabasePlatformName());
        self::assertEquals('SQLServer', $this->sqlsrv->getDatabasePlatformName(Sqlsrv::NAME_FORMAT_NATURAL));
    }

    /**
     * @depends testRegisterConnection
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::getConnection
     */
    public function testGetConnection($mockConnection)
    {
        $conn = new \Laminas\Db\Adapter\Driver\Sqlsrv\Connection([]);
        $this->sqlsrv->registerConnection($conn);
        self::assertSame($conn, $this->sqlsrv->getConnection());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::createStatement
     * @todo   Implement testGetPrepareType().
     */
    public function testCreateStatement()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::createResult
     * @todo   Implement testGetPrepareType().
     */
    public function testCreateResult()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::getPrepareType
     * @todo   Implement testGetPrepareType().
     */
    public function testGetPrepareType()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::formatParameterName
     * @todo   Implement testFormatParameterName().
     */
    public function testFormatParameterName()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv::getLastGeneratedValue
     * @todo   Implement testGetLastGeneratedValue().
     */
    public function testGetLastGeneratedValue()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
