<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\TableGateway\Feature;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\Feature\MasterSlaveFeature;
use PHPUnit\Framework\TestCase;

class MasterSlaveFeatureTest extends TestCase
{
    /** @var AdapterInterface */
    protected $mockMasterAdapter;

    /** @var AdapterInterface */
    protected $mockSlaveAdapter;

    /** @var MasterSlaveFeature */
    protected $feature;

    /** @var \Laminas\Db\TableGateway\TableGateway */
    protected $table;

    protected function setUp()
    {
        $this->mockMasterAdapter = $this->getMockBuilder('Laminas\Db\Adapter\AdapterInterface')->getMock();

        $mockStatement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue(
            $mockStatement
        ));
        $this->mockMasterAdapter->expects($this->any())->method('getDriver')->will($this->returnValue($mockDriver));
        $this->mockMasterAdapter->expects($this->any())->method('getPlatform')->will($this->returnValue(
            new \Laminas\Db\Adapter\Platform\Sql92()
        ));

        $this->mockSlaveAdapter = $this->getMockBuilder('Laminas\Db\Adapter\AdapterInterface')->getMock();

        $mockStatement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue(
            $mockStatement
        ));
        $this->mockSlaveAdapter->expects($this->any())->method('getDriver')->will($this->returnValue($mockDriver));
        $this->mockSlaveAdapter->expects($this->any())->method('getPlatform')->will($this->returnValue(
            new \Laminas\Db\Adapter\Platform\Sql92()
        ));

        $this->feature = new MasterSlaveFeature($this->mockSlaveAdapter);
    }

    public function testPostInitialize()
    {
        /** @var $table \Laminas\Db\TableGateway\TableGateway */
        $this->getMockForAbstractClass(
            'Laminas\Db\TableGateway\TableGateway',
            ['foo', $this->mockMasterAdapter, $this->feature]
        );
        // postInitialize is run
        self::assertSame($this->mockSlaveAdapter, $this->feature->getSlaveSql()->getAdapter());
    }

    public function testPreSelect()
    {
        $table = $this->getMockForAbstractClass(
            'Laminas\Db\TableGateway\TableGateway',
            ['foo', $this->mockMasterAdapter, $this->feature]
        );

        $this->mockSlaveAdapter->getDriver()->createStatement()
            ->expects($this->once())->method('execute')->will($this->returnValue(
                $this->getMockBuilder('Laminas\Db\ResultSet\ResultSet')->getMock()
            ));
        $table->select('foo = bar');
    }

    public function testPostSelect()
    {
        $table = $this->getMockForAbstractClass(
            'Laminas\Db\TableGateway\TableGateway',
            ['foo', $this->mockMasterAdapter, $this->feature]
        );
        $this->mockSlaveAdapter->getDriver()->createStatement()
            ->expects($this->once())->method('execute')->will($this->returnValue(
                $this->getMockBuilder('Laminas\Db\ResultSet\ResultSet')->getMock()
            ));

        $masterSql = $table->getSql();
        $table->select('foo = bar');

        // test that the sql object is restored
        self::assertSame($masterSql, $table->getSql());
    }
}
