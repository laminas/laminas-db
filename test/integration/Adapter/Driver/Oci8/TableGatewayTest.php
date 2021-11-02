<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

use function count;

class TableGatewayTest extends TestCase
{
    use TraitSetup;
    protected $adapter;

    /**
     * @covers \Laminas\Db\TableGateway\TableGateway::__construct
     */
    public function testConstructor()
    {
        $adapter = $this->createAdapter();
        $tableGateway = new TableGateway('test', $adapter);
        $this->assertInstanceOf(TableGateway::class, $tableGateway);
    }

    /**
     * @covers \Laminas\Db\TableGateway\TableGateway::select
     */
    public function testSelect()
    {
        $adapter = $this->createAdapter();
        $tableGateway = new TableGateway('test', $adapter);
        $rowset = $tableGateway->select();

        $this->assertTrue(count($rowset) > 0);
        foreach ($rowset as $row) {
            $this->assertTrue(isset($row->ID));
            $this->assertNotEmpty(isset($row->NAME));
            $this->assertNotEmpty(isset($row->VALUE));
        }
    }

    /**
     * @covers \Laminas\Db\TableGateway\TableGateway::insert
     * @covers \Laminas\Db\TableGateway\TableGateway::select
     */
    public function testInsert()
    {
        $adapter = $this->createAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $tableGateway = new TableGateway('test', $adapter);

            $rowset = $tableGateway->select();
            $data = [
                'name' => 'test_name',
                'value' => 'test_value',
            ];
            $affectedRows = $tableGateway->insert($data);
            $this->assertEquals(1, $affectedRows);

            /**
             *
             * @note: this section is not available for OCI8-connection
             * @see: \Laminas\Db\Adapter\Driver\Oci8\Connection::getLastGeneratedValue
             **/
//            $lastInsertId = $tableGateway->getLastInsertValue();
            $lastInsertId = $this->getMaxIdFrom($adapter, 'test');
            $rowSet = $tableGateway->select([
                'id' => $lastInsertId
            ]);
            $row = $rowSet->current();

            foreach ($data as $key => $value) {
                $upperKey = mb_strtoupper($key);
                $expected = $row->$upperKey;
                $this->assertEquals($expected, $value);
            }
        } finally {
            $adapter->getDriver()->getConnection()->rollback();
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }

    /**
     * @see https://github.com/zendframework/zend-db/issues/35
     * @see https://github.com/zendframework/zend-db/pull/178
     *
     * @return mixed
     */
    public function testInsertWithExtendedCharsetFieldName()
    {
        $adapter = $this->createAdapter();
        $tableGateway = new TableGateway('test_charset', $adapter);

        $affectedRows = $tableGateway->insert([
            'field$' => 'test_value1',
            'field_' => 'test_value2',
        ]);
        $this->assertEquals(1, $affectedRows);

        //$result = $tableGateway->getLastInsertValue(); // Is not avaliable.
        $result = $this->getMaxIdFrom($adapter, 'test_charset');

        return $result;
    }

    /**
     *
     * @param Adapter $adapter
     * @param string $tableName
     * @param string $idFieldName
     * @return mixed
     */
    public function getMaxIdFrom(Adapter $adapter, string $tableName, $idFieldName = 'id')
    {
        /**
         * @warning:  "$adapter->query" does not work in the same transaction.
         */
        //$resultSet = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        //$resultArr = $resultSet->toArray();
        //$result = $resultArr[0]['LAST_ID'];

        $connection = $adapter->getDriver()->getConnection();
        $sql = sprintf('select max(%s) as last_id from %s',
            $idFieldName,
            $tableName
        );
        $resultSet = $connection->execute($sql);
        $row = $resultSet->current();
        $result = $row['LAST_ID'];

        return $result;
    }

    /**
     * @depends testInsertWithExtendedCharsetFieldName
     * @param mixed $id
     */
    public function testUpdateWithExtendedCharsetFieldName($id)
    {
        $adapter = $this->createAdapter();
        $tableGateway = new TableGateway('test_charset', $adapter);

        $data = [
            'FIELD$' => 'test_value3',
            'FIELD_' => 'test_value4',
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
        $getFixedTableIdentifier = function (TableIdentifier $table)
        {
            $tableName = $table->getTable();
            $tableName = mb_strtoupper($tableName);
            $schema = $table->getSchema();
            $schema ?: mb_strtoupper($schema);
            $result = new TableIdentifier($tableName, $schema);
            return $result;
        };

        if (is_string($table)) {
            $table = mb_strtoupper($table);
        } else if (is_array($table)) {
            foreach ($table as $key => $val) {
                if (is_string($val)) {
                    $table[$key] = mb_strtoupper($val);
                } elseif ($val instanceof TableIdentifier) {
                    $table[$key] = $getFixedTableIdentifier($val);
                }

            }
        } else if ($table instanceof TableIdentifier) {
            $table = $getFixedTableIdentifier($table);
        }
        $adapter = $this->createAdapter();
        $tableGateway = new TableGateway($table, $adapter, new MetadataFeature());

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
