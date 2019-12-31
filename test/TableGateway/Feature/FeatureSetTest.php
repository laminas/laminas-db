<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\TableGateway\Feature;

use Laminas\Db\Metadata\Object\ConstraintObject;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\Db\TableGateway\Feature\MasterSlaveFeature;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use Laminas\Db\TableGateway\Feature\SequenceFeature;
use ReflectionClass;

class FeatureSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover FeatureSet::addFeature
     * @group Laminas-4993
     */
    public function testAddFeatureThatFeatureDoesNotHaveTableGatewayButFeatureSetHas()
    {
        $mockMasterAdapter = $this->getMock('Laminas\Db\Adapter\AdapterInterface');

        $mockStatement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $mockDriver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue(
            $mockStatement
        ));
        $mockMasterAdapter->expects($this->any())->method('getDriver')->will($this->returnValue($mockDriver));
        $mockMasterAdapter->expects($this->any())->method('getPlatform')->will($this->returnValue(new \Laminas\Db\Adapter\Platform\Sql92()));

        $mockSlaveAdapter = $this->getMock('Laminas\Db\Adapter\AdapterInterface');

        $mockStatement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $mockDriver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue(
            $mockStatement
        ));
        $mockSlaveAdapter->expects($this->any())->method('getDriver')->will($this->returnValue($mockDriver));
        $mockSlaveAdapter->expects($this->any())->method('getPlatform')->will($this->returnValue(new \Laminas\Db\Adapter\Platform\Sql92()));

        $tableGatewayMock = $this->getMockForAbstractClass('Laminas\Db\TableGateway\AbstractTableGateway');

        //feature doesn't have tableGateway, but FeatureSet has
        $feature = new MasterSlaveFeature($mockSlaveAdapter);

        $featureSet = new FeatureSet;
        $featureSet->setTableGateway($tableGatewayMock);

        $this->assertInstanceOf('Laminas\Db\TableGateway\Feature\FeatureSet', $featureSet->addFeature($feature));
    }

    /**
     * @cover FeatureSet::addFeature
     * @group Laminas-4993
     */
    public function testAddFeatureThatFeatureHasTableGatewayButFeatureSetDoesNotHave()
    {
        $tableGatewayMock = $this->getMockForAbstractClass('Laminas\Db\TableGateway\AbstractTableGateway');

        $metadataMock = $this->getMock('Laminas\Db\Metadata\MetadataInterface');
        $metadataMock->expects($this->any())->method('getColumnNames')->will($this->returnValue(['id', 'name']));

        $constraintObject = new ConstraintObject('id_pk', 'table');
        $constraintObject->setColumns(['id']);
        $constraintObject->setType('PRIMARY KEY');

        $metadataMock->expects($this->any())->method('getConstraints')->will($this->returnValue([$constraintObject]));

        //feature have tableGateway, but FeatureSet doesn't has
        $feature = new MetadataFeature($metadataMock);
        $feature->setTableGateway($tableGatewayMock);

        $featureSet = new FeatureSet;
        $this->assertInstanceOf('Laminas\Db\TableGateway\Feature\FeatureSet', $featureSet->addFeature($feature));
    }

    /**
     * @covers Laminas\Db\TableGateway\Feature\FeatureSet::canCallMagicCall
     */
    public function testCanCallMagicCallReturnsTrueForAddedMethodOfAddedFeature()
    {
        $feature = new SequenceFeature('id', 'table_sequence');
        $featureSet = new FeatureSet;
        $featureSet->addFeature($feature);

        $this->assertTrue(
            $featureSet->canCallMagicCall('lastSequenceId'),
            "Should have been able to call lastSequenceId from the Sequence Feature"
        );
    }

    /**
     * @covers Laminas\Db\TableGateway\Feature\FeatureSet::canCallMagicCall
     */
    public function testCanCallMagicCallReturnsFalseForAddedMethodOfAddedFeature()
    {
        $feature = new SequenceFeature('id', 'table_sequence');
        $featureSet = new FeatureSet;
        $featureSet->addFeature($feature);

        $this->assertFalse(
            $featureSet->canCallMagicCall('postInitialize'),
            "Should have been able to call postInitialize from the MetaData Feature"
        );
    }

    /**
     * @covers Laminas\Db\TableGateway\Feature\FeatureSet::canCallMagicCall
     */
    public function testCanCallMagicCallReturnsFalseWhenNoFeaturesHaveBeenAdded()
    {
        $featureSet = new FeatureSet;
        $this->assertFalse(
            $featureSet->canCallMagicCall('lastSequenceId')
        );
    }

    /**
     * @covers Laminas\Db\TableGateway\Feature\FeatureSet::callMagicCall
     */
    public function testCallMagicCallSucceedsForValidMethodOfAddedFeature()
    {
        $sequenceName = 'table_sequence';

        $platformMock = $this->getMock('Laminas\Db\Adapter\Platform\Postgresql');
        $platformMock->expects($this->any())
            ->method('getName')->will($this->returnValue('PostgreSQL'));

        $resultMock = $this->getMock('Laminas\Db\Adapter\Driver\Pgsql\Result');
        $resultMock->expects($this->any())
            ->method('current')
            ->will($this->returnValue(['currval' => 1]));

        $statementMock = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $statementMock->expects($this->any())
            ->method('prepare')
            ->with('SELECT CURRVAL(\'' . $sequenceName . '\')');
        $statementMock->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($resultMock));

        $adapterMock = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock->expects($this->any())
            ->method('getPlatform')->will($this->returnValue($platformMock));
        $adapterMock->expects($this->any())
            ->method('createStatement')->will($this->returnValue($statementMock));

        $tableGatewayMock = $this->getMockBuilder('Laminas\Db\TableGateway\AbstractTableGateway')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionClass = new ReflectionClass('Laminas\Db\TableGateway\AbstractTableGateway');
        $reflectionProperty = $reflectionClass->getProperty('adapter');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($tableGatewayMock, $adapterMock);

        $feature = new SequenceFeature('id', 'table_sequence');
        $feature->setTableGateway($tableGatewayMock);
        $featureSet = new FeatureSet;
        $featureSet->addFeature($feature);
        $this->assertEquals(1, $featureSet->callMagicCall('lastSequenceId', null));
    }
}
