<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\IbmDb2;

use Laminas\Db\Adapter\Driver\IbmDb2\IbmDb2;
use Laminas\Db\Adapter\Driver\IbmDb2\Statement;
use Laminas\Db\Adapter\Exception\RuntimeException;
use Laminas\Db\Adapter\ParameterContainer;
use PHPUnit\Framework\TestCase;

include __DIR__ . '/TestAsset/Db2Functions.php';

class StatementTest extends TestCase
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
        // store current error_reporting value as we may change it
        // in a test
        $this->currentErrorReporting = error_reporting();
        $this->statement = new Statement;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        // ensure error_reporting is set back to correct value
        error_reporting($this->currentErrorReporting);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::setDriver
     */
    public function testSetDriver()
    {
        self::assertEquals($this->statement, $this->statement->setDriver(new IbmDb2([])));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::setParameterContainer
     */
    public function testSetParameterContainer()
    {
        self::assertSame($this->statement, $this->statement->setParameterContainer(new ParameterContainer));
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::getParameterContainer
     * @todo   Implement testGetParameterContainer().
     */
    public function testGetParameterContainer()
    {
        $container = new ParameterContainer;
        $this->statement->setParameterContainer($container);
        self::assertSame($container, $this->statement->getParameterContainer());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::getResource
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
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::setSql
     * @todo   Implement testSetSql().
     */
    public function testSetSql()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::getSql
     * @todo   Implement testGetSql().
     */
    public function testGetSql()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::prepare
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::isPrepared
     */
    public function testPrepare()
    {
        $sql = "SELECT 'foo' FROM SYSIBM.SYSDUMMY1";
        $this->statement->prepare($sql);
        $this->assertTrue($this->statement->isPrepared());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::prepare
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::isPrepared
     */
    public function testPreparingTwiceErrors()
    {
        $sql = "SELECT 'foo' FROM SYSIBM.SYSDUMMY1";
        $this->statement->prepare($sql);
        $this->assertTrue($this->statement->isPrepared());

        $this->expectException(
            RuntimeException::class,
            'This statement has been prepared already'
        );
        $this->statement->prepare($sql);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::prepare
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::setSql
     */
    public function testPrepareThrowsRuntimeExceptionOnInvalidSql()
    {
        $sql = "INVALID SQL";
        $this->statement->setSql($sql);

        $this->expectException(
            RuntimeException::class,
            'SQL is invalid. Error message'
        );
        $this->statement->prepare();
    }

    /**
     * If error_reporting() is turned off, then the error handler will not
     * be called, but a RuntimeException will still be generated as the
     * resource is false
     *
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::prepare
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::setSql
     */
    public function testPrepareThrowsRuntimeExceptionOnInvalidSqlWithErrorReportingDisabled()
    {
        error_reporting(0);
        $sql = "INVALID SQL";
        $this->statement->setSql($sql);

        $this->expectException(
            RuntimeException::class,
            'Error message'
        );
        $this->statement->prepare();
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\IbmDb2\Statement::execute
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
