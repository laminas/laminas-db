<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\TableGateway;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\Sql\Update;
use Laminas\Db\TableGateway\Feature;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

class TableGatewayTest extends TestCase
{
    protected $mockAdapter;

    protected function setUp()
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ResultInterface')->getMock();
        $mockStatement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));
        $mockConnection = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ConnectionInterface')->getMock();
        $mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        // setup mock adapter
        $this->mockAdapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();
    }

    /**
     * Beside other tests checks for plain string table identifier
     */
    public function testConstructor()
    {
        // constructor with only required args
        $table = new TableGateway(
            'foo',
            $this->mockAdapter
        );

        self::assertEquals('foo', $table->getTable());
        self::assertSame($this->mockAdapter, $table->getAdapter());
        self::assertInstanceOf('Laminas\Db\TableGateway\Feature\FeatureSet', $table->getFeatureSet());
        self::assertInstanceOf('Laminas\Db\ResultSet\ResultSet', $table->getResultSetPrototype());
        self::assertInstanceOf('Laminas\Db\Sql\Sql', $table->getSql());

        // injecting all args
        $table = new TableGateway(
            'foo',
            $this->mockAdapter,
            $featureSet = new Feature\FeatureSet,
            $resultSet = new ResultSet,
            $sql = new Sql($this->mockAdapter, 'foo')
        );

        self::assertEquals('foo', $table->getTable());
        self::assertSame($this->mockAdapter, $table->getAdapter());
        self::assertSame($featureSet, $table->getFeatureSet());
        self::assertSame($resultSet, $table->getResultSetPrototype());
        self::assertSame($sql, $table->getSql());

        // constructor expects exception
        $this->expectException('Laminas\Db\TableGateway\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Table name must be a string or an instance of Laminas\Db\Sql\TableIdentifier');
        new TableGateway(
            null,
            $this->mockAdapter
        );
    }

    /**
     * @group 6726
     * @group 6740
     */
    public function testTableAsString()
    {
        $ti = 'fooTable.barSchema';
        // constructor with only required args
        $table = new TableGateway(
            $ti,
            $this->mockAdapter
        );

        self::assertEquals($ti, $table->getTable());
    }

    /**
     * @group 6726
     * @group 6740
     */
    public function testTableAsTableIdentifierObject()
    {
        $ti = new TableIdentifier('fooTable', 'barSchema');
        // constructor with only required args
        $table = new TableGateway(
            $ti,
            $this->mockAdapter
        );

        self::assertEquals($ti, $table->getTable());
    }

    /**
     * @group 6726
     * @group 6740
     */
    public function testTableAsAliasedTableIdentifierObject()
    {
        $aliasedTI = ['foo' => new TableIdentifier('fooTable', 'barSchema')];
        // constructor with only required args
        $table = new TableGateway(
            $aliasedTI,
            $this->mockAdapter
        );

        self::assertEquals($aliasedTI, $table->getTable());
    }

    public function aliasedTables()
    {
        $identifier = new TableIdentifier('Users');
        return [
            'simple-alias'     => [['U' => 'Users'], 'Users'],
            'identifier-alias' => [['U' => $identifier], $identifier],
        ];
    }

    /**
     * @group 7311
     * @dataProvider aliasedTables
     */
    public function testInsertShouldResetTableToUnaliasedTable($tableValue, $expected)
    {
        $insert = new Insert();
        $insert->into($tableValue);

        $result = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ResultInterface')
            ->getMock();
        $result->expects($this->once())
            ->method('getAffectedRows')
            ->will($this->returnValue(1));

        $statement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')
            ->getMock();
        $statement->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));

        $statementExpectation = function ($insert) use ($expected, $statement) {
            $state = $insert->getRawState();
            self::assertSame($expected, $state['table']);
            return $statement;
        };

        $sql = $this->getMockBuilder('Laminas\Db\Sql\Sql')
            ->disableOriginalConstructor()
            ->getMock();
        $sql->expects($this->atLeastOnce())
            ->method('getTable')
            ->will($this->returnValue($tableValue));
        $sql->expects($this->once())
            ->method('insert')
            ->will($this->returnValue($insert));
        $sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($this->equalTo($insert))
            ->will($this->returnCallback($statementExpectation));

        $table = new TableGateway(
            $tableValue,
            $this->mockAdapter,
            null,
            null,
            $sql
        );

        $result = $table->insert([
            'foo' => 'FOO',
        ]);

        $state = $insert->getRawState();
        self::assertInternalType('array', $state['table']);
        self::assertEquals(
            $tableValue,
            $state['table']
        );
    }

    /**
     * @dataProvider aliasedTables
     */
    public function testUpdateShouldResetTableToUnaliasedTable($tableValue, $expected)
    {
        $update = new Update();
        $update->table($tableValue);

        $result = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ResultInterface')
            ->getMock();
        $result->expects($this->once())
            ->method('getAffectedRows')
            ->will($this->returnValue(1));

        $statement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')
            ->getMock();
        $statement->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));

        $statementExpectation = function ($update) use ($expected, $statement) {
            $state = $update->getRawState();
            self::assertSame($expected, $state['table']);
            return $statement;
        };

        $sql = $this->getMockBuilder('Laminas\Db\Sql\Sql')
            ->disableOriginalConstructor()
            ->getMock();
        $sql->expects($this->atLeastOnce())
            ->method('getTable')
            ->will($this->returnValue($tableValue));
        $sql->expects($this->once())
            ->method('update')
            ->will($this->returnValue($update));
        $sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($this->equalTo($update))
            ->will($this->returnCallback($statementExpectation));

        $table = new TableGateway(
            $tableValue,
            $this->mockAdapter,
            null,
            null,
            $sql
        );

        $result = $table->update([
            'foo' => 'FOO',
        ], [
            'bar' => 'BAR',
        ]);

        $state = $update->getRawState();
        self::assertInternalType('array', $state['table']);
        self::assertEquals(
            $tableValue,
            $state['table']
        );
    }

    /**
     * @dataProvider aliasedTables
     */
    public function testDeleteShouldResetTableToUnaliasedTable($tableValue, $expected)
    {
        $delete = new Delete();
        $delete->from($tableValue);

        $result = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ResultInterface')
            ->getMock();
        $result->expects($this->once())
            ->method('getAffectedRows')
            ->will($this->returnValue(1));

        $statement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')
            ->getMock();
        $statement->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));

        $statementExpectation = function ($delete) use ($expected, $statement) {
            $state = $delete->getRawState();
            self::assertSame($expected, $state['table']);
            return $statement;
        };

        $sql = $this->getMockBuilder('Laminas\Db\Sql\Sql')
            ->disableOriginalConstructor()
            ->getMock();
        $sql->expects($this->atLeastOnce())
            ->method('getTable')
            ->will($this->returnValue($tableValue));
        $sql->expects($this->once())
            ->method('delete')
            ->will($this->returnValue($delete));
        $sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($this->equalTo($delete))
            ->will($this->returnCallback($statementExpectation));

        $table = new TableGateway(
            $tableValue,
            $this->mockAdapter,
            null,
            null,
            $sql
        );

        $result = $table->delete([
            'foo' => 'FOO',
        ]);

        $state = $delete->getRawState();
        self::assertInternalType('array', $state['table']);
        self::assertEquals(
            $tableValue,
            $state['table']
        );
    }
}
