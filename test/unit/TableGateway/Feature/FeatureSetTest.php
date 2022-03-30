<?php

namespace LaminasTest\Db\TableGateway\Feature;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Pgsql\Result;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\Platform\Postgresql;
use Laminas\Db\Adapter\Platform\Sql92;
use Laminas\Db\Metadata\MetadataInterface;
use Laminas\Db\Metadata\Object\ConstraintObject;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\Db\TableGateway\Feature\MasterSlaveFeature;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use Laminas\Db\TableGateway\Feature\SequenceFeature;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FeatureSetTest extends TestCase
{
    /**
     * @cover FeatureSet::addFeature
     * @group Laminas-4993
     */
    public function testAddFeatureThatFeatureDoesNotHaveTableGatewayButFeatureSetHas()
    {
        $mockMasterAdapter = $this->getMockBuilder(AdapterInterface::class)->getMock();

        $mockStatement = $this->getMockBuilder(StatementInterface::class)->getMock();
        $mockDriver    = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue(
            $mockStatement
        ));
        $mockMasterAdapter->expects($this->any())->method('getDriver')->will($this->returnValue($mockDriver));
        $mockMasterAdapter->expects($this->any())->method('getPlatform')->will($this->returnValue(
            new Sql92()
        ));

        $mockSlaveAdapter = $this->getMockBuilder(AdapterInterface::class)->getMock();

        $mockStatement = $this->getMockBuilder(StatementInterface::class)->getMock();
        $mockDriver    = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue(
            $mockStatement
        ));
        $mockSlaveAdapter->expects($this->any())->method('getDriver')->will($this->returnValue($mockDriver));
        $mockSlaveAdapter->expects($this->any())->method('getPlatform')->will($this->returnValue(
            new Sql92()
        ));

        $tableGatewayMock = $this->getMockForAbstractClass(AbstractTableGateway::class);

        //feature doesn't have tableGateway, but FeatureSet has
        $feature = new MasterSlaveFeature($mockSlaveAdapter);

        $featureSet = new FeatureSet();
        $featureSet->setTableGateway($tableGatewayMock);

        self::assertInstanceOf(FeatureSet::class, $featureSet->addFeature($feature));
    }

    /**
     * @cover FeatureSet::addFeature
     * @group Laminas-4993
     */
    public function testAddFeatureThatFeatureHasTableGatewayButFeatureSetDoesNotHave()
    {
        $tableGatewayMock = $this->getMockForAbstractClass(AbstractTableGateway::class);

        $metadataMock = $this->getMockBuilder(MetadataInterface::class)->getMock();
        $metadataMock->expects($this->any())->method('getColumnNames')->will($this->returnValue(['id', 'name']));

        $constraintObject = new ConstraintObject('id_pk', 'table');
        $constraintObject->setColumns(['id']);
        $constraintObject->setType('PRIMARY KEY');

        $metadataMock->expects($this->any())->method('getConstraints')->will($this->returnValue([$constraintObject]));

        //feature have tableGateway, but FeatureSet doesn't has
        $feature = new MetadataFeature($metadataMock);
        $feature->setTableGateway($tableGatewayMock);

        $featureSet = new FeatureSet();
        self::assertInstanceOf(FeatureSet::class, $featureSet->addFeature($feature));
    }

    /**
     * @covers \Laminas\Db\TableGateway\Feature\FeatureSet::canCallMagicCall
     */
    public function testCanCallMagicCallReturnsTrueForAddedMethodOfAddedFeature()
    {
        $feature    = new SequenceFeature('id', 'table_sequence');
        $featureSet = new FeatureSet();
        $featureSet->addFeature($feature);

        self::assertTrue(
            $featureSet->canCallMagicCall('lastSequenceId'),
            "Should have been able to call lastSequenceId from the Sequence Feature"
        );
    }

    /**
     * @covers \Laminas\Db\TableGateway\Feature\FeatureSet::canCallMagicCall
     */
    public function testCanCallMagicCallReturnsFalseForAddedMethodOfAddedFeature()
    {
        $feature    = new SequenceFeature('id', 'table_sequence');
        $featureSet = new FeatureSet();
        $featureSet->addFeature($feature);

        self::assertFalse(
            $featureSet->canCallMagicCall('postInitialize'),
            "Should have been able to call postInitialize from the MetaData Feature"
        );
    }

    /**
     * @covers \Laminas\Db\TableGateway\Feature\FeatureSet::canCallMagicCall
     */
    public function testCanCallMagicCallReturnsFalseWhenNoFeaturesHaveBeenAdded()
    {
        $featureSet = new FeatureSet();
        self::assertFalse(
            $featureSet->canCallMagicCall('lastSequenceId')
        );
    }

    /**
     * @covers \Laminas\Db\TableGateway\Feature\FeatureSet::callMagicCall
     */
    public function testCallMagicCallSucceedsForValidMethodOfAddedFeature()
    {
        $sequenceName = 'table_sequence';

        $platformMock = $this->getMockBuilder(Postgresql::class)->getMock();
        $platformMock->expects($this->any())
            ->method('getName')->will($this->returnValue('PostgreSQL'));

        $resultMock = $this->getMockBuilder(Result::class)->getMock();
        $resultMock->expects($this->any())
            ->method('current')
            ->will($this->returnValue(['currval' => 1]));

        $statementMock = $this->getMockBuilder(StatementInterface::class)->getMock();
        $statementMock->expects($this->any())
            ->method('prepare')
            ->with('SELECT CURRVAL(\'' . $sequenceName . '\')');
        $statementMock->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($resultMock));

        $adapterMock = $this->getMockBuilder(Adapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock->expects($this->any())
            ->method('getPlatform')->will($this->returnValue($platformMock));
        $adapterMock->expects($this->any())
            ->method('createStatement')->will($this->returnValue($statementMock));

        $tableGatewayMock = $this->getMockBuilder(AbstractTableGateway::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionClass    = new ReflectionClass(AbstractTableGateway::class);
        $reflectionProperty = $reflectionClass->getProperty('adapter');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($tableGatewayMock, $adapterMock);

        $feature = new SequenceFeature('id', 'table_sequence');
        $feature->setTableGateway($tableGatewayMock);
        $featureSet = new FeatureSet();
        $featureSet->addFeature($feature);
        self::assertEquals(1, $featureSet->callMagicCall('lastSequenceId', null));
    }
}
