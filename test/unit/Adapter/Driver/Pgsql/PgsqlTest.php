<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pgsql;

use Laminas\Db\Adapter\Driver\Pgsql\Pgsql;
use PHPUnit\Framework\TestCase;

class PgsqlTest extends TestCase
{
    /**
     * @var Pgsql
     */
    protected $pgsql;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->pgsql = new Pgsql([]);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::checkEnvironment
     */
    public function testCheckEnvironment()
    {
        if (! extension_loaded('pgsql')) {
            $this->expectException('Laminas\Db\Adapter\Exception\RuntimeException');
        }
        $this->pgsql->checkEnvironment();
        self::assertTrue(true, 'No exception was thrown');
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::registerConnection
     */
    public function testRegisterConnection()
    {
        $mockConnection = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\Pgsql\Connection',
            [[]],
            '',
            true,
            true,
            true,
            ['setDriver']
        );
        $mockConnection->expects($this->once())->method('setDriver')->with($this->equalTo($this->pgsql));
        self::assertSame($this->pgsql, $this->pgsql->registerConnection($mockConnection));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::registerStatementPrototype
     */
    public function testRegisterStatementPrototype()
    {
        $this->pgsql = new Pgsql([]);
        $mockStatement = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\Pgsql\Statement',
            [],
            '',
            true,
            true,
            true,
            ['setDriver']
        );
        $mockStatement->expects($this->once())->method('setDriver')->with($this->equalTo($this->pgsql));
        self::assertSame($this->pgsql, $this->pgsql->registerStatementPrototype($mockStatement));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::registerResultPrototype
     */
    public function testRegisterResultPrototype()
    {
        $this->pgsql = new Pgsql([]);
        $mockStatement = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\Pgsql\Result',
            [],
            '',
            true,
            true,
            true,
            ['setDriver']
        );
        self::assertSame($this->pgsql, $this->pgsql->registerResultPrototype($mockStatement));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::getDatabasePlatformName
     */
    public function testGetDatabasePlatformName()
    {
        $this->pgsql = new Pgsql([]);
        self::assertEquals('Postgresql', $this->pgsql->getDatabasePlatformName());
        self::assertEquals('PostgreSQL', $this->pgsql->getDatabasePlatformName(Pgsql::NAME_FORMAT_NATURAL));
    }

    /**
     * @depends testRegisterConnection
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::getConnection
     */
    public function testGetConnection($mockConnection)
    {
        $conn = new \Laminas\Db\Adapter\Driver\Pgsql\Connection([]);
        $this->pgsql->registerConnection($conn);
        self::assertSame($conn, $this->pgsql->getConnection());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::createStatement
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
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::createResult
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
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::getPrepareType
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
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::formatParameterName
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
     * @covers \Laminas\Db\Adapter\Driver\Pgsql\Pgsql::getLastGeneratedValue
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
