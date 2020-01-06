<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Profiler;
use LaminasTest\Db\TestAsset\TemporaryResultSet;
use PHPUnit\Framework\TestCase;

class AdapterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockDriver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockPlatform;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockConnection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStatement;

    /**
     * @var Adapter
     */
    protected $adapter;

    protected function setUp()
    {
        $this->mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $this->mockConnection = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ConnectionInterface')->getMock();
        $this->mockDriver->expects($this->any())->method('checkEnvironment')->will($this->returnValue(true));
        $this->mockDriver->expects($this->any())->method('getConnection')
            ->will($this->returnValue($this->mockConnection));
        $this->mockPlatform = $this->getMockBuilder('Laminas\Db\Adapter\Platform\PlatformInterface')->getMock();
        $this->mockStatement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $this->mockDriver->expects($this->any())->method('createStatement')
            ->will($this->returnValue($this->mockStatement));

        $this->adapter = new Adapter($this->mockDriver, $this->mockPlatform);
    }

    /**
     * @testdox unit test: Test setProfiler() will store profiler
     * @covers \Laminas\Db\Adapter\Adapter::setProfiler
     */
    public function testSetProfiler()
    {
        $ret = $this->adapter->setProfiler(new Profiler\Profiler());
        self::assertSame($this->adapter, $ret);
    }

    /**
     * @testdox unit test: Test getProfiler() will store profiler
     * @covers \Laminas\Db\Adapter\Adapter::getProfiler
     */
    public function testGetProfiler()
    {
        $this->adapter->setProfiler($profiler = new Profiler\Profiler());
        self::assertSame($profiler, $this->adapter->getProfiler());

        $adapter = new Adapter(['driver' => $this->mockDriver, 'profiler' => true], $this->mockPlatform);
        self::assertInstanceOf('Laminas\Db\Adapter\Profiler\Profiler', $adapter->getProfiler());
    }

    /**
     * @testdox unit test: Test createDriverFromParameters() will create proper driver type
     * @covers \Laminas\Db\Adapter\Adapter::createDriver
     */
    public function testCreateDriver()
    {
        if (extension_loaded('mysqli')) {
            $adapter = new Adapter(['driver' => 'mysqli'], $this->mockPlatform);
            self::assertInstanceOf('Laminas\Db\Adapter\Driver\Mysqli\Mysqli', $adapter->driver);
            unset($adapter);
        }

        if (extension_loaded('pgsql')) {
            $adapter = new Adapter(['driver' => 'pgsql'], $this->mockPlatform);
            self::assertInstanceOf('Laminas\Db\Adapter\Driver\Pgsql\Pgsql', $adapter->driver);
            unset($adapter);
        }

        if (extension_loaded('sqlsrv')) {
            $adapter = new Adapter(['driver' => 'sqlsrv'], $this->mockPlatform);
            self::assertInstanceOf('Laminas\Db\Adapter\Driver\Sqlsrv\Sqlsrv', $adapter->driver);
            unset($adapter);
        }

        if (extension_loaded('pdo')) {
            $adapter = new Adapter(['driver' => 'pdo_sqlite'], $this->mockPlatform);
            self::assertInstanceOf('Laminas\Db\Adapter\Driver\Pdo\Pdo', $adapter->driver);
            unset($adapter);
        }
    }

    /**
     * @testdox unit test: Test createPlatformFromDriver() will create proper platform from driver
     * @covers \Laminas\Db\Adapter\Adapter::createPlatform
     */
    public function testCreatePlatform()
    {
        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('Mysql'));
        $adapter = new Adapter($driver);
        self::assertInstanceOf('Laminas\Db\Adapter\Platform\Mysql', $adapter->platform);
        unset($adapter, $driver);

        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('SqlServer'));
        $adapter = new Adapter($driver);
        self::assertInstanceOf('Laminas\Db\Adapter\Platform\SqlServer', $adapter->platform);
        unset($adapter, $driver);

        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('Postgresql'));
        $adapter = new Adapter($driver);
        self::assertInstanceOf('Laminas\Db\Adapter\Platform\Postgresql', $adapter->platform);
        unset($adapter, $driver);

        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('Sqlite'));
        $adapter = new Adapter($driver);
        self::assertInstanceOf('Laminas\Db\Adapter\Platform\Sqlite', $adapter->platform);
        unset($adapter, $driver);

        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('IbmDb2'));
        $adapter = new Adapter($driver);
        self::assertInstanceOf('Laminas\Db\Adapter\Platform\IbmDb2', $adapter->platform);
        unset($adapter, $driver);

        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('Oracle'));
        $adapter = new Adapter($driver);
        self::assertInstanceOf('Laminas\Db\Adapter\Platform\Oracle', $adapter->platform);
        unset($adapter, $driver);

        $driver = clone $this->mockDriver;
        $driver->expects($this->any())->method('getDatabasePlatformName')->will($this->returnValue('Foo'));
        $adapter = new Adapter($driver);
        self::assertInstanceOf('Laminas\Db\Adapter\Platform\Sql92', $adapter->platform);
        unset($adapter, $driver);

        // ensure platform can created via string, and also that it passed in options to platform object
        $driver = [
            'driver' => 'pdo_sqlite',
            'platform' => 'Oracle',
            'platform_options' => ['quote_identifiers' => false],
        ];
        $adapter = new Adapter($driver);
        self::assertInstanceOf('Laminas\Db\Adapter\Platform\Oracle', $adapter->platform);
        self::assertEquals('foo', $adapter->getPlatform()->quoteIdentifier('foo'));
        unset($adapter, $driver);
    }


    /**
     * @testdox unit test: Test getDriver() will return driver object
     * @covers \Laminas\Db\Adapter\Adapter::getDriver
     */
    public function testGetDriver()
    {
        self::assertSame($this->mockDriver, $this->adapter->getDriver());
    }

    /**
     * @testdox unit test: Test getPlatform() returns platform object
     * @covers \Laminas\Db\Adapter\Adapter::getPlatform
     */
    public function testGetPlatform()
    {
        self::assertSame($this->mockPlatform, $this->adapter->getPlatform());
    }

    /**
     * @testdox unit test: Test getPlatform() returns platform object
     * @covers \Laminas\Db\Adapter\Adapter::getQueryResultSetPrototype
     */
    public function testGetQueryResultSetPrototype()
    {
        self::assertInstanceOf('Laminas\Db\ResultSet\ResultSetInterface', $this->adapter->getQueryResultSetPrototype());
    }

    /**
     * @testdox unit test: Test getCurrentSchema() returns current schema from connection object
     * @covers \Laminas\Db\Adapter\Adapter::getCurrentSchema
     */
    public function testGetCurrentSchema()
    {
        $this->mockConnection->expects($this->any())->method('getCurrentSchema')->will($this->returnValue('FooSchema'));
        self::assertEquals('FooSchema', $this->adapter->getCurrentSchema());
    }

    /**
     * @testdox unit test: Test query() in prepare mode produces a statement object
     * @covers \Laminas\Db\Adapter\Adapter::query
     */
    public function testQueryWhenPreparedProducesStatement()
    {
        $s = $this->adapter->query('SELECT foo');
        self::assertSame($this->mockStatement, $s);
    }

    /**
     * @testdox unit test: Test query() in prepare mode, with array of parameters, produces a result object
     * @covers \Laminas\Db\Adapter\Adapter::query
     */
    public function testQueryWhenPreparedWithParameterArrayProducesResult()
    {
        $parray = ['bar' => 'foo'];
        $sql = 'SELECT foo, :bar';
        $statement = $this->getMockBuilder('\Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $result = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ResultInterface')->getMock();
        $this->mockDriver->expects($this->any())->method('createStatement')
            ->with($sql)->will($this->returnValue($statement));
        $this->mockStatement->expects($this->any())->method('execute')->will($this->returnValue($result));

        $r = $this->adapter->query($sql, $parray);
        self::assertSame($result, $r);
    }

    /**
     * @testdox unit test: Test query() in prepare mode, with ParameterContainer, produces a result object
     * @covers \Laminas\Db\Adapter\Adapter::query
     */
    public function testQueryWhenPreparedWithParameterContainerProducesResult()
    {
        $sql = 'SELECT foo';
        $parameterContainer = $this->getMockBuilder('Laminas\Db\Adapter\ParameterContainer')->getMock();
        $result = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ResultInterface')->getMock();
        $this->mockDriver->expects($this->any())->method('createStatement')
            ->with($sql)->will($this->returnValue($this->mockStatement));
        $this->mockStatement->expects($this->any())->method('execute')->will($this->returnValue($result));
        $result->expects($this->any())->method('isQueryResult')->will($this->returnValue(true));

        $r = $this->adapter->query($sql, $parameterContainer);
        self::assertInstanceOf('Laminas\Db\ResultSet\ResultSet', $r);
    }

    /**
     * @testdox unit test: Test query() in execute mode produces a driver result object
     * @covers \Laminas\Db\Adapter\Adapter::query
     */
    public function testQueryWhenExecutedProducesAResult()
    {
        $sql = 'SELECT foo';
        $result = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ResultInterface')->getMock();
        $this->mockConnection->expects($this->any())->method('execute')->with($sql)->will($this->returnValue($result));

        $r = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        self::assertSame($result, $r);
    }

    /**
     * @testdox unit test: Test query() in execute mode produces a resultset object
     * @covers \Laminas\Db\Adapter\Adapter::query
     */
    public function testQueryWhenExecutedProducesAResultSetObjectWhenResultIsQuery()
    {
        $sql = 'SELECT foo';

        $result = $this->getMockBuilder('Laminas\Db\Adapter\Driver\ResultInterface')->getMock();
        $this->mockConnection->expects($this->any())->method('execute')->with($sql)->will($this->returnValue($result));
        $result->expects($this->any())->method('isQueryResult')->will($this->returnValue(true));

        $r = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        self::assertInstanceOf('Laminas\Db\ResultSet\ResultSet', $r);

        $r = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE, new TemporaryResultSet());
        self::assertInstanceOf('LaminasTest\Db\TestAsset\TemporaryResultSet', $r);
    }

    /**
     * @testdox unit test: Test createStatement() produces a statement object
     * @covers \Laminas\Db\Adapter\Adapter::createStatement
     */
    public function testCreateStatement()
    {
        self::assertSame($this->mockStatement, $this->adapter->createStatement());
    }

    /**
     * @testdox unit test: Test __get() works
     * @covers \Laminas\Db\Adapter\Adapter::__get
     */
    // @codingStandardsIgnoreStart
    public function test__get()
    {
        // @codingStandardsIgnoreEnd
        self::assertSame($this->mockDriver, $this->adapter->driver);
        self::assertSame($this->mockDriver, $this->adapter->DrivER);
        self::assertSame($this->mockPlatform, $this->adapter->PlatForm);
        self::assertSame($this->mockPlatform, $this->adapter->platform);

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Invalid magic');
        $this->adapter->foo;
    }
}
