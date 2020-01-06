<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\TableGateway\Feature;

use Laminas\Db\Metadata\MetadataInterface;
use Laminas\Db\Metadata\Object\ConstraintObject;
use Laminas\Db\Metadata\Object\TableObject;
use Laminas\Db\Metadata\Object\ViewObject;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class MetadataFeatureTest extends TestCase
{
    /**
     * @group integration-test
     */
    public function testPostInitialize()
    {
        $tableGatewayMock = $this->getMockForAbstractClass('Laminas\Db\TableGateway\AbstractTableGateway');
        $metadataMock = $this->getMockBuilder('Laminas\Db\Metadata\MetadataInterface')->getMock();
        $metadataMock->expects($this->any())->method('getColumnNames')->will($this->returnValue(['id', 'name']));

        $constraintObject = new ConstraintObject('id_pk', 'table');
        $constraintObject->setColumns(['id']);
        $constraintObject->setType('PRIMARY KEY');

        $metadataMock->expects($this->any())->method('getConstraints')->will($this->returnValue([$constraintObject]));

        $feature = new MetadataFeature($metadataMock);
        $feature->setTableGateway($tableGatewayMock);
        $feature->postInitialize();

        self::assertEquals(['id', 'name'], $tableGatewayMock->getColumns());
    }

    public function testPostInitializeRecordsPrimaryKeyColumnToSharedMetadata()
    {
        /** @var AbstractTableGateway $tableGatewayMock */
        $tableGatewayMock = $this->getMockForAbstractClass(AbstractTableGateway::class);
        $metadataMock = $this->getMockBuilder(MetadataInterface::class)->getMock();
        $metadataMock->expects($this->any())->method('getColumnNames')->will($this->returnValue(['id', 'name']));
        $metadataMock->expects($this->any())
            ->method('getTable')
            ->will($this->returnValue(new TableObject('foo')));


        $constraintObject = new ConstraintObject('id_pk', 'table');
        $constraintObject->setColumns(['id']);
        $constraintObject->setType('PRIMARY KEY');

        $metadataMock->expects($this->any())->method('getConstraints')->will($this->returnValue([$constraintObject]));

        $feature = new MetadataFeature($metadataMock);
        $feature->setTableGateway($tableGatewayMock);
        $feature->postInitialize();

        $r = new ReflectionProperty(MetadataFeature::class, 'sharedData');
        $r->setAccessible(true);
        $sharedData = $r->getValue($feature);

        self::assertTrue(
            isset($sharedData['metadata']['primaryKey']),
            'Shared data must have metadata entry for primary key'
        );
        self::assertSame($sharedData['metadata']['primaryKey'], 'id');
    }

    public function testPostInitializeRecordsListOfColumnsInPrimaryKeyToSharedMetadata()
    {
        /** @var AbstractTableGateway $tableGatewayMock */
        $tableGatewayMock = $this->getMockForAbstractClass(AbstractTableGateway::class);
        $metadataMock = $this->getMockBuilder(MetadataInterface::class)->getMock();
        $metadataMock->expects($this->any())->method('getColumnNames')->will($this->returnValue(['id', 'name']));
        $metadataMock->expects($this->any())
            ->method('getTable')
            ->will($this->returnValue(new TableObject('foo')));


        $constraintObject = new ConstraintObject('id_pk', 'table');
        $constraintObject->setColumns(['composite', 'id']);
        $constraintObject->setType('PRIMARY KEY');

        $metadataMock->expects($this->any())->method('getConstraints')->will($this->returnValue([$constraintObject]));

        $feature = new MetadataFeature($metadataMock);
        $feature->setTableGateway($tableGatewayMock);
        $feature->postInitialize();

        $r = new ReflectionProperty(MetadataFeature::class, 'sharedData');
        $r->setAccessible(true);
        $sharedData = $r->getValue($feature);

        self::assertTrue(
            isset($sharedData['metadata']['primaryKey']),
            'Shared data must have metadata entry for primary key'
        );
        self::assertEquals($sharedData['metadata']['primaryKey'], ['composite', 'id']);
    }

    public function testPostInitializeSkipsPrimaryKeyCheckIfNotTable()
    {
        /** @var AbstractTableGateway $tableGatewayMock */
        $tableGatewayMock = $this->getMockForAbstractClass(AbstractTableGateway::class);
        $metadataMock = $this->getMockBuilder(MetadataInterface::class)->getMock();
        $metadataMock->expects($this->any())->method('getColumnNames')->will($this->returnValue(['id', 'name']));
        $metadataMock->expects($this->any())
            ->method('getTable')
            ->will($this->returnValue(new ViewObject('foo')));

        $metadataMock->expects($this->never())->method('getConstraints');

        $feature = new MetadataFeature($metadataMock);
        $feature->setTableGateway($tableGatewayMock);
        $feature->postInitialize();
    }
}
