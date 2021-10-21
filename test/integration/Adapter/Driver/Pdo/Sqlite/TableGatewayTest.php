<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Sqlite;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

use function count;

/**
 * @property Adapter adapter
 */
class TableGatewayTest extends TestCase
{
    use AdapterTrait;

    /**
     * @covers \Laminas\Db\TableGateway\TableGateway::__construct
     */
    public function testConstructor()
    {
        $tableGateway = new TableGateway('test', $this->adapter);
        $this->assertInstanceOf(TableGateway::class, $tableGateway);
    }

    public function testSelectWithEmptyCurrentWithoutBufferResult()
    {
        $adapter = $this->adapter;

        $tableGateway = new TableGateway('test', $adapter);
        $rowset = $tableGateway->select('id = 0');
        $result = $rowset->current();
        $this->assertNull($result);

        $adapter->getDriver()->getConnection()->disconnect();
    }

    /**
     * @covers \Laminas\Db\TableGateway\TableGateway::select
     */
    public function testSelect()
    {
        $tableGateway = new TableGateway('test', $this->adapter);
        $rowset = $tableGateway->select();

        $this->assertTrue(count($rowset) > 0);
        foreach ($rowset as $row) {
            $this->assertTrue(isset($row->id));
            $this->assertNotEmpty(isset($row->name));
            $this->assertNotEmpty(isset($row->value));
        }
    }

    /**
     * @covers \Laminas\Db\TableGateway\TableGateway::insert
     * @covers \Laminas\Db\TableGateway\TableGateway::select
     */
    public function testInsert()
    {
        $this->adapter->getDriver()->getConnection()->beginTransaction();
        $tableGateway = new TableGateway('test', $this->adapter);
        $data = [
            'name' => 'test_name',
            'value' => 'test_value',
        ];
        $affectedRows = $tableGateway->insert($data);
        $this->assertEquals(1, $affectedRows);

        $rowSet = $tableGateway->select(['id' => $tableGateway->getLastInsertValue()]);
        $row = $rowSet->current();

        foreach ($data as $key => $value) {
            $this->assertEquals($row->$key, $value);
        }
        $this->adapter->getDriver()->getConnection()->rollback();
    }

    /**
     * @dataProvider tableProvider
     * @param string|TableIdentifier|array $table
     */
    public function testTableGatewayWithMetadataFeature($table)
    {
        $tableGateway = new TableGateway($table, $this->adapter, new MetadataFeature());

        self::assertInstanceOf(TableGateway::class, $tableGateway);
        self::assertSame($table, $tableGateway->getTable());
    }

    /** @psalm-return array<string, array{0: mixed}> */
    public function tableProvider(): array
    {
        return [
            'string' => ['test'],
            'aliased string' => [['foo' => 'test']],
            'TableIdentifier' => [new TableIdentifier('test')],
            'aliased TableIdentifier' => [['foo' => new TableIdentifier('test')]],
        ];
    }
}
