<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Driver\Pdo\Statement;
use PHPUnit\Framework\TestCase;

class StatementIntegrationTest extends TestCase
{
    /**
     * @var Statement
     */
    protected $statement;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pdoStatementMock;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $driver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\Pdo\Pdo')
            ->setMethods(['createResult'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->statement = new Statement;
        $this->statement->setDriver($driver);
        $this->statement->initialize(new TestAsset\CtorlessPdo(
            $this->pdoStatementMock = $this->getMockBuilder('PDOStatement')
                ->setMethods(['execute', 'bindParam'])
                ->getMock()
        ));
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
            $this->equalTo(':foo'),
            $this->equalTo(false),
            $this->equalTo(\PDO::PARAM_BOOL)
        );
        $this->statement->execute(['foo' => false]);
    }

    public function testStatementExecuteWillUsePdoStrByDefaultWhenBinding()
    {
        $this->pdoStatementMock->expects($this->any())->method('bindParam')->with(
            $this->equalTo(':foo'),
            $this->equalTo('bar'),
            $this->equalTo(\PDO::PARAM_STR)
        );
        $this->statement->execute(['foo' => 'bar']);
    }

    public function testStatementExecuteWillUsePdoStrForStringIntegerWhenBinding()
    {
        $this->pdoStatementMock->expects($this->any())->method('bindParam')->with(
            $this->equalTo(':foo'),
            $this->equalTo('123'),
            $this->equalTo(\PDO::PARAM_STR)
        );
        $this->statement->execute(['foo' => '123']);
    }

    public function testStatementExecuteWillUsePdoIntForIntWhenBinding()
    {
        $this->pdoStatementMock->expects($this->any())->method('bindParam')->with(
            $this->equalTo(':foo'),
            $this->equalTo(123),
            $this->equalTo(\PDO::PARAM_INT)
        );
        $this->statement->execute(['foo' => 123]);
    }
}
