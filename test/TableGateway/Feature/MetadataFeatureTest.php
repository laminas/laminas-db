<?php

namespace LaminasTest\Db\TableGateway\Feature;

use Laminas\Db\Metadata\Object\ConstraintObject;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use PHPUnit_Framework_TestCase;

class MetadataFeatureTest extends PHPUnit_Framework_TestCase
{


    /**
     * @group integration-test
     */
    public function testPostInitialize()
    {
        $tableGatewayMock = $this->getMockForAbstractClass('Laminas\Db\TableGateway\AbstractTableGateway');

        $metadataMock = $this->getMock('Laminas\Db\Metadata\MetadataInterface');
        $metadataMock->expects($this->any())->method('getColumnNames')->will($this->returnValue(array('id', 'name')));

        $constraintObject = new ConstraintObject('id_pk', 'table');
        $constraintObject->setColumns(array('id'));
        $constraintObject->setType('PRIMARY KEY');

        $metadataMock->expects($this->any())->method('getConstraints')->will($this->returnValue(array($constraintObject)));

        $feature = new MetadataFeature($metadataMock);
        $feature->setTableGateway($tableGatewayMock);
        $feature->postInitialize();

        $this->assertEquals(array('id', 'name'), $tableGatewayMock->getColumns());
    }

}
