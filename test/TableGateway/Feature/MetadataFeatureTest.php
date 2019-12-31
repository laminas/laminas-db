<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\TableGateway\Feature;

use Laminas\Db\Metadata\Object\ConstraintObject;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use PHPUnit\Framework\TestCase;

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
}
