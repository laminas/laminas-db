<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Driver\Oci8\Oci8;
use PHPUnit\Framework\TestCase;

class Oci8Test extends TestCase
{
    /**
     * @var Oci8
     */
    protected $oci8;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->oci8 = new Oci8([]);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::registerConnection
     */
    public function testRegisterConnection()
    {
        $mockConnection = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\Oci8\Connection',
            [[]],
            '',
            true,
            true,
            true,
            ['setDriver']
        );
        $mockConnection->expects($this->once())->method('setDriver')->with($this->equalTo($this->oci8));
        self::assertSame($this->oci8, $this->oci8->registerConnection($mockConnection));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::registerStatementPrototype
     */
    public function testRegisterStatementPrototype()
    {
        $this->oci8 = new Oci8([]);
        $mockStatement = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\Oci8\Statement',
            [],
            '',
            true,
            true,
            true,
            ['setDriver']
        );
        $mockStatement->expects($this->once())->method('setDriver')->with($this->equalTo($this->oci8));
        self::assertSame($this->oci8, $this->oci8->registerStatementPrototype($mockStatement));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::registerResultPrototype
     */
    public function testRegisterResultPrototype()
    {
        $this->oci8 = new Oci8([]);
        $mockStatement = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\Oci8\Result',
            [],
            '',
            true,
            true,
            true,
            ['setDriver']
        );
        self::assertSame($this->oci8, $this->oci8->registerResultPrototype($mockStatement));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::getDatabasePlatformName
     */
    public function testGetDatabasePlatformName()
    {
        $this->oci8 = new Oci8([]);
        self::assertEquals('Oracle', $this->oci8->getDatabasePlatformName());
        self::assertEquals('Oracle', $this->oci8->getDatabasePlatformName(Oci8::NAME_FORMAT_NATURAL));
    }

    /**
     * @depends testRegisterConnection
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::getConnection
     */
    public function testGetConnection($mockConnection)
    {
        $conn = new \Laminas\Db\Adapter\Driver\Oci8\Connection([]);
        $this->oci8->registerConnection($conn);
        self::assertSame($conn, $this->oci8->getConnection());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::createStatement
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::createResult
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::getPrepareType
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::formatParameterName
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
     * @covers \Laminas\Db\Adapter\Driver\Oci8\Oci8::getLastGeneratedValue
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
