<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Sql;
use LaminasTest\Db\TestAsset;
use PHPUnit\Framework\TestCase;

class SqlTest extends TestCase
{
    protected $mockAdapter;

    /**
     * Sql object
     * @var Sql
     */
    protected $sql;

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
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));

        // setup mock adapter
        $this->mockAdapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods()
            ->setConstructorArgs([$mockDriver, new TestAsset\TrustingSql92Platform()])
            ->getMock();

        $this->sql = new Sql($this->mockAdapter, 'foo');
    }

    /**
     * @covers \Laminas\Db\Sql\Sql::__construct
     */
    // @codingStandardsIgnoreStart
    public function test__construct()
    {
        // @codingStandardsIgnoreEnd
        $sql = new Sql($this->mockAdapter);

        self::assertFalse($sql->hasTable());

        $sql->setTable('foo');
        self::assertSame('foo', $sql->getTable());

        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Table must be a string, array or instance of TableIdentifier.');
        $sql->setTable(null);
    }

    /**
     * @covers \Laminas\Db\Sql\Sql::select
     */
    public function testSelect()
    {
        $select = $this->sql->select();
        self::assertInstanceOf('Laminas\Db\Sql\Select', $select);
        self::assertSame('foo', $select->getRawState('table'));

        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage(
            'This Sql object is intended to work with only the table "foo" provided at construction time.'
        );
        $this->sql->select('bar');
    }

    /**
     * @covers \Laminas\Db\Sql\Sql::insert
     */
    public function testInsert()
    {
        $insert = $this->sql->insert();
        self::assertInstanceOf('Laminas\Db\Sql\Insert', $insert);
        self::assertSame('foo', $insert->getRawState('table'));

        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage(
            'This Sql object is intended to work with only the table "foo" provided at construction time.'
        );
        $this->sql->insert('bar');
    }

    /**
     * @covers \Laminas\Db\Sql\Sql::update
     */
    public function testUpdate()
    {
        $update = $this->sql->update();
        self::assertInstanceOf('Laminas\Db\Sql\Update', $update);
        self::assertSame('foo', $update->getRawState('table'));

        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage(
            'This Sql object is intended to work with only the table "foo" provided at construction time.'
        );
        $this->sql->update('bar');
    }

    /**
     * @covers \Laminas\Db\Sql\Sql::delete
     */
    public function testDelete()
    {
        $delete = $this->sql->delete();

        self::assertInstanceOf('Laminas\Db\Sql\Delete', $delete);
        self::assertSame('foo', $delete->getRawState('table'));

        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage(
            'This Sql object is intended to work with only the table "foo" provided at construction time.'
        );
        $this->sql->delete('bar');
    }

    /**
     * @covers \Laminas\Db\Sql\Sql::prepareStatementForSqlObject
     */
    public function testPrepareStatementForSqlObject()
    {
        $insert = $this->sql->insert()->columns(['foo'])->values(['foo' => 'bar']);
        $stmt = $this->sql->prepareStatementForSqlObject($insert);
        self::assertInstanceOf('Laminas\Db\Adapter\Driver\StatementInterface', $stmt);
    }

    /**
     * @group 6890
     */
    public function testForDifferentAdapters()
    {
        $adapterSql92     = $this->getAdapterForPlatform('sql92');
        $adapterMySql     = $this->getAdapterForPlatform('MySql');
        $adapterOracle    = $this->getAdapterForPlatform('Oracle');
        $adapterSqlServer = $this->getAdapterForPlatform('SqlServer');

        $select = $this->sql->select()->offset(10);

        // Default
        self::assertEquals(
            'SELECT "foo".* FROM "foo" OFFSET \'10\'',
            $this->sql->buildSqlString($select)
        );
        $this->mockAdapter->getDriver()->createStatement()->expects($this->any())->method('setSql')
                ->with($this->equalTo('SELECT "foo".* FROM "foo" OFFSET ?'));
        $this->sql->prepareStatementForSqlObject($select);

        // Sql92
        self::assertEquals(
            'SELECT "foo".* FROM "foo" OFFSET \'10\'',
            $this->sql->buildSqlString($select, $adapterSql92)
        );
        $adapterSql92->getDriver()->createStatement()->expects($this->any())->method('setSql')
                ->with($this->equalTo('SELECT "foo".* FROM "foo" OFFSET ?'));
        $this->sql->prepareStatementForSqlObject($select, null, $adapterSql92);

        // MySql
        self::assertEquals(
            'SELECT `foo`.* FROM `foo` LIMIT 18446744073709551615 OFFSET 10',
            $this->sql->buildSqlString($select, $adapterMySql)
        );
        $adapterMySql->getDriver()->createStatement()->expects($this->any())->method('setSql')
                ->with($this->equalTo('SELECT `foo`.* FROM `foo` LIMIT 18446744073709551615 OFFSET ?'));
        $this->sql->prepareStatementForSqlObject($select, null, $adapterMySql);

        // Oracle
        self::assertEquals(
            'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b ) WHERE b_rownum > (10)',
            $this->sql->buildSqlString($select, $adapterOracle)
        );
        // @codingStandardsIgnoreStart
        $adapterOracle->getDriver()->createStatement()->expects($this->any())->method('setSql')
                ->with($this->equalTo('SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b ) WHERE b_rownum > (:offset)'));
        // @codingStandardsIgnoreEnd
        $this->sql->prepareStatementForSqlObject($select, null, $adapterOracle);

        // SqlServer
        self::assertContains(
            'WHERE [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__LAMINAS_ROW_NUMBER] BETWEEN 10+1 AND 0+10',
            $this->sql->buildSqlString($select, $adapterSqlServer)
        );
        $adapterSqlServer->getDriver()->createStatement()->expects($this->any())->method('setSql')
                ->with($this->stringContains(
                    'WHERE [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__LAMINAS_ROW_NUMBER] BETWEEN ?+1 AND ?+?'
                ));
        $this->sql->prepareStatementForSqlObject($select, null, $adapterSqlServer);
    }

    /**
     * Data provider
     *
     * @param string $platform
     *
     * @return Adapter
     */
    protected function getAdapterForPlatform($platform)
    {
        switch ($platform) {
            case 'sql92':
                $platform  = new TestAsset\TrustingSql92Platform();
                break;
            case 'MySql':
                $platform  = new TestAsset\TrustingMysqlPlatform();
                break;
            case 'Oracle':
                $platform  = new TestAsset\TrustingOraclePlatform();
                break;
            case 'SqlServer':
                $platform  = new TestAsset\TrustingSqlServerPlatform();
                break;
            default:
                $platform = null;
        }

        $mockStatement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));

        return new Adapter($mockDriver, $platform);
    }
}
