<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Driver\Pdo\Statement;

class StatementIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Statement
     */
    protected $statement;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pdoStatementMock = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->statement = new Statement;
        $this->statement->setDriver($this->getMock('Laminas\Db\Adapter\Driver\Pdo\Pdo', array('createResult'), array(), '', false));
        $this->statement->initialize(new TestAsset\CtorlessPdo(
            $this->pdoStatementMock = $this->getMock('PDOStatement', array('execute', 'bindParam')))
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testStatementExecuteWillConvertPhpBoolToPdoBoolWhenBinding()
    {
        $this->pdoStatementMock->expects($this->any())->method('bindParam')->with(
            $this->equalTo('foo'),
            $this->equalTo(false),
            $this->equalTo(\PDO::PARAM_BOOL)
        );
        $this->statement->execute(array('foo' => false));
    }

    public function testStatementExecuteWillUsePdoStrByDefaultWhenBinding()
    {
        $this->pdoStatementMock->expects($this->any())->method('bindParam')->with(
            $this->equalTo('foo'),
            $this->equalTo('bar'),
            $this->equalTo(\PDO::PARAM_STR)
        );
        $this->statement->execute(array('foo' => 'bar'));
    }
}
