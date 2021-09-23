<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Postgresql;

use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\Db\TableGateway\Feature\SequenceFeature;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;
use Laminas\Db\Sql\Select;

class TableGatewayTest extends TestCase
{
    use AdapterTrait;

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

    /**
     * @covers \Laminas\Db\TableGateway\TableGateway::select
     */
    public function testSelectFetchColumn()
    {
        $tableGateway = new TableGateway('test', $this->adapter);

        $select = new Select();
        $select->from('test');
        $select->columns([
            'myid' => 'id'
        ]);
        $select->limit(1);

        $statement = $tableGateway->getSql()
            ->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        $result->setFetchMode(\PDO::FETCH_COLUMN);
        $this->assertSame(1, $result->next());
    }
}
