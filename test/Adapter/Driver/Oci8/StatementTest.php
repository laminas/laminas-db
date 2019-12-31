<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Driver\Oci8\Oci8;
use Laminas\Db\Adapter\Driver\Oci8\Statement;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Profiler\Profiler;

/**
 * @group integrationOracle
 */
class StatementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Statement
     */
    protected $statement;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->statement = new Statement;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::setDriver
     */
    public function testSetDriver()
    {
        $this->assertEquals($this->statement, $this->statement->setDriver(new Oci8([])));
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::setProfiler
     */
    public function testSetProfiler()
    {
        $this->assertEquals($this->statement, $this->statement->setProfiler(new Profiler()));
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::getProfiler
     */
    public function testGetProfiler()
    {
        $profiler = new Profiler();
        $this->statement->setProfiler($profiler);
        $this->assertEquals($profiler, $this->statement->getProfiler());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::initialize
     */
    public function testInitialize()
    {
        $oci8 = new Oci8([]);
        $this->assertEquals($this->statement, $this->statement->initialize($oci8));
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::setSql
     */
    public function testSetSql()
    {
        $this->assertEquals($this->statement, $this->statement->setSql('select * from table'));
        $this->assertEquals('select * from table', $this->statement->getSql());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::setParameterContainer
     */
    public function testSetParameterContainer()
    {
        $this->assertSame($this->statement, $this->statement->setParameterContainer(new ParameterContainer));
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::getParameterContainer
     * @todo   Implement testGetParameterContainer().
     */
    public function testGetParameterContainer()
    {
        $container = new ParameterContainer;
        $this->statement->setParameterContainer($container);
        $this->assertSame($container, $this->statement->getParameterContainer());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::getResource
     * @todo   Implement testGetResource().
     */
    public function testGetResource()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::getSql
     * @todo   Implement testGetSql().
     */
    public function testGetSql()
    {
        $this->assertEquals($this->statement, $this->statement->setSql('select * from table'));
        $this->assertEquals('select * from table', $this->statement->getSql());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::prepare
     * @todo   Implement testPrepare().
     */
    public function testPrepare()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::isPrepared
     */
    public function testIsPrepared()
    {
        $this->assertFalse($this->statement->isPrepared());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Oci8\Statement::execute
     * @todo   Implement testExecute().
     */
    public function testExecute()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
