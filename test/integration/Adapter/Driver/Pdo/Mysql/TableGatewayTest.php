<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Mysql;

use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

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
        $tableGateway = new TableGateway('test', $this->adapter);

        $rowset = $tableGateway->select();
        $data = [
            'name'  => 'test_name',
            'value' => 'test_value'
        ];
        $affectedRows = $tableGateway->insert($data);
        $this->assertEquals(1, $affectedRows);

        $rowSet = $tableGateway->select(['id' => $tableGateway->getLastInsertValue()]);
        $row = $rowSet->current();

        foreach ($data as $key => $value) {
            $this->assertEquals($row->$key, $value);
        }
    }

    /**
     * @see https://github.com/zendframework/zend-db/issues/35
     * @see https://github.com/zendframework/zend-db/pull/178
     */
    public function testInsertWithExtendedCharsetFieldName()
    {
        $tableGateway = new TableGateway('test_charset', $this->adapter);

        $affectedRows = $tableGateway->insert([
            'field$' => 'test_value1',
            'field_' => 'test_value2'
        ]);
        $this->assertEquals(1, $affectedRows);
        return $tableGateway->getLastInsertValue();
    }

    /**
     * @depends testInsertWithExtendedCharsetFieldName
     */
    public function testUpdateWithExtendedCharsetFieldName($id)
    {
        $tableGateway = new TableGateway('test_charset', $this->adapter);

        $data = [
            'field$' => 'test_value3',
            'field_' => 'test_value4'
        ];
        $affectedRows = $tableGateway->update($data, ['id' => $id]);
        $this->assertEquals(1, $affectedRows);

        $rowSet = $tableGateway->select(['id' => $id]);
        $row = $rowSet->current();

        foreach ($data as $key => $value) {
            $this->assertEquals($row->$key, $value);
        }
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

    public function tableProvider()
    {
        return [
            'string'                  => ['test'],
            'aliased string'          => [['foo' => 'test']],
            'TableIdentifier'         => [new TableIdentifier('test')],
            'aliased TableIdentifier' => [['foo' => new TableIdentifier('test')]],
        ];
    }
}
