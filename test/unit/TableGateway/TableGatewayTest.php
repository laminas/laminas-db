<?php

namespace LaminasTest\Db\TableGateway;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ConnectionInterface;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\Sql\Update;
use Laminas\Db\TableGateway\Exception\InvalidArgumentException;
use Laminas\Db\TableGateway\Feature;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TableGatewayTest extends TestCase
{
    /** @var Adapter&MockObject */
    protected $mockAdapter;

    protected function setUp(): void
    {
        // mock the adapter, driver, and parts
        $mockResult    = $this->getMockBuilder(ResultInterface::class)->getMock();
        $mockStatement = $this->getMockBuilder(StatementInterface::class)->getMock();
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));
        $mockConnection = $this->getMockBuilder(ConnectionInterface::class)->getMock();
        $mockDriver     = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        // setup mock adapter
        $this->mockAdapter = $this->getMockBuilder(Adapter::class)
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
        self::assertInstanceOf(FeatureSet::class, $table->getFeatureSet());
        self::assertInstanceOf(ResultSet::class, $table->getResultSetPrototype());
        self::assertInstanceOf(Sql::class, $table->getSql());

        // injecting all args
        $table          = new TableGateway(
            'foo',
            $this->mockAdapter,
            $featureSet = new Feature\FeatureSet(),
            $resultSet  = new ResultSet(),
            $sql        = new Sql($this->mockAdapter, 'foo')
        );

        self::assertEquals('foo', $table->getTable());
        self::assertSame($this->mockAdapter, $table->getAdapter());
        self::assertSame($featureSet, $table->getFeatureSet());
        self::assertSame($resultSet, $table->getResultSetPrototype());
        self::assertSame($sql, $table->getSql());

        // constructor expects exception
        $this->expectException(InvalidArgumentException::class);
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
        // phpcs:disable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps
        $aliasedTI = ['foo' => new TableIdentifier('fooTable', 'barSchema')];
        // constructor with only required args
        $table = new TableGateway(
            $aliasedTI,
            $this->mockAdapter
        );

        self::assertEquals($aliasedTI, $table->getTable());
        // phpcs:enable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps
    }

    /**
     * @psalm-return array<string, array{
     *     0: array<string, string|TableIdentifier>,
     *     1: string|TableIdentifier
     * }>
     */
    public function aliasedTables(): array
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
     * @param array<string, string|TableIdentifier> $tableValue
     * @param string|TableIdentifier $expected
     */
    public function testInsertShouldResetTableToUnaliasedTable(array $tableValue, $expected)
    {
        $insert = new Insert();
        $insert->into($tableValue);

        $result = $this->getMockBuilder(ResultInterface::class)
            ->getMock();
        $result->expects($this->once())
            ->method('getAffectedRows')
            ->will($this->returnValue(1));

        $statement = $this->getMockBuilder(StatementInterface::class)
            ->getMock();
        $statement->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));

        $statementExpectation = function ($insert) use ($expected, $statement) {
            $state = $insert->getRawState();
            self::assertSame($expected, $state['table']);
            return $statement;
        };

        $sql = $this->getMockBuilder(Sql::class)
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
        self::assertIsArray($state['table']);
        self::assertEquals(
            $tableValue,
            $state['table']
        );
    }

    /**
     * @dataProvider aliasedTables
     * @param array<string, string|TableIdentifier> $tableValue
     * @param string|TableIdentifier $expected
     */
    public function testUpdateShouldResetTableToUnaliasedTable(array $tableValue, $expected)
    {
        $update = new Update();
        $update->table($tableValue);

        $result = $this->getMockBuilder(ResultInterface::class)
            ->getMock();
        $result->expects($this->once())
            ->method('getAffectedRows')
            ->will($this->returnValue(1));

        $statement = $this->getMockBuilder(StatementInterface::class)
            ->getMock();
        $statement->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));

        $statementExpectation = function ($update) use ($expected, $statement) {
            $state = $update->getRawState();
            self::assertSame($expected, $state['table']);
            return $statement;
        };

        $sql = $this->getMockBuilder(Sql::class)
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
        self::assertIsArray($state['table']);
        self::assertEquals(
            $tableValue,
            $state['table']
        );
    }

    /**
     * @dataProvider aliasedTables
     * @param array<string, string|TableIdentifier> $tableValue
     * @param string|TableIdentifier $expected
     */
    public function testDeleteShouldResetTableToUnaliasedTable(array $tableValue, $expected)
    {
        $delete = new Delete();
        $delete->from($tableValue);

        $result = $this->getMockBuilder(ResultInterface::class)
            ->getMock();
        $result->expects($this->once())
            ->method('getAffectedRows')
            ->will($this->returnValue(1));

        $statement = $this->getMockBuilder(StatementInterface::class)
            ->getMock();
        $statement->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($result));

        $statementExpectation = function ($delete) use ($expected, $statement) {
            $state = $delete->getRawState();
            self::assertSame($expected, $state['table']);
            return $statement;
        };

        $sql = $this->getMockBuilder(Sql::class)
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
        self::assertIsArray($state['table']);
        self::assertEquals(
            $tableValue,
            $state['table']
        );
    }
}
