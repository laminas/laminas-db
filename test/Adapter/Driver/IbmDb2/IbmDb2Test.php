<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\IbmDb2;

use Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2;

class IbmDb2Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IbmDb2
     */
    protected $ibmdb2 = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->ibmdb2 = new IbmDb2(array());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::registerConnection
     */
    public function testRegisterConnection()
    {
        $mockConnection = $this->getMockForAbstractClass('Laminas\Db\Adapter\Driver\IbmDb2\Connection', array(array()), '', true, true, true, array('setDriver'));
        $mockConnection->expects($this->once())->method('setDriver')->with($this->equalTo($this->ibmdb2));
        $this->assertSame($this->ibmdb2, $this->ibmdb2->registerConnection($mockConnection));
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::registerStatementPrototype
     */
    public function testRegisterStatementPrototype()
    {
        $this->ibmdb2 = new IbmDb2(array());
        $mockStatement = $this->getMockForAbstractClass('Laminas\Db\Adapter\Driver\IbmDb2\Statement', array(), '', true, true, true, array('setDriver'));
        $mockStatement->expects($this->once())->method('setDriver')->with($this->equalTo($this->ibmdb2));
        $this->assertSame($this->ibmdb2, $this->ibmdb2->registerStatementPrototype($mockStatement));
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::registerResultPrototype
     */
    public function testRegisterResultPrototype()
    {
        $this->ibmdb2 = new IbmDb2(array());
        $mockStatement = $this->getMockForAbstractClass('Laminas\Db\Adapter\Driver\IbmDb2\Result', array(), '', true, true, true, array('setDriver'));
        $this->assertSame($this->ibmdb2, $this->ibmdb2->registerResultPrototype($mockStatement));
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::getDatabasePlatformName
     */
    public function testGetDatabasePlatformName()
    {
        $this->ibmdb2 = new IbmDb2(array());
        $this->assertEquals('IbmDb2', $this->ibmdb2->getDatabasePlatformName());
        $this->assertEquals('IBM DB2', $this->ibmdb2->getDatabasePlatformName(IbmDb2::NAME_FORMAT_NATURAL));
    }

    /**
     * @depends testRegisterConnection
     * @covers Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::getConnection
     */
    public function testGetConnection($mockConnection)
    {
        $conn = new \Laminas\Db\Adapter\Driver\IbmDb2\Connection(array());
        $this->ibmdb2->registerConnection($conn);
        $this->assertSame($conn, $this->ibmdb2->getConnection());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::createStatement
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
     * @covers Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::createResult
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
     * @covers Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::getPrepareType
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
     * @covers Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::formatParameterName
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
     * @covers Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2::getLastGeneratedValue
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
