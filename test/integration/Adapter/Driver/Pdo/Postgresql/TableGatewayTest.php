<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Postgresql;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\Db\TableGateway\Feature\SequenceFeature;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

class TableGatewayTest extends TestCase
{
    use AdapterTrait;

    /** @var Adapter */
    protected $adapter;
    public function testLastInsertValue()
    {
        $table      = new TableIdentifier('test_seq');
        $featureSet = new FeatureSet();
        $featureSet->addFeature(new SequenceFeature('id', 'test_seq_id_seq'));

        $tableGateway = new TableGateway($table, $this->adapter, $featureSet);

        $tableGateway->insert(['foo' => 'bar']);
        self::assertSame(1, $tableGateway->getLastInsertValue());

        $tableGateway->insert(['foo' => 'baz']);
        self::assertSame(2, $tableGateway->getLastInsertValue());
    }
}
