<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

use function count;
use function is_array;
use function is_string;
use function mb_strtoupper;
use function sprintf;

class TableGatewayTest extends TestCase
{
    use TraitSetup;

    /**
     * @covers \Laminas\Db\TableGateway\TableGateway::__construct
     */
    public function testConstructor()
    {
        $adapter      = $this->createAdapter();
        $tableGateway = new TableGateway('test', $adapter);
        $this->assertInstanceOf(TableGateway::class, $tableGateway);
    }

    /**
     * @covers \Laminas\Db\TableGateway\TableGateway::select
     */
    public function testSelect()
    {
        $adapter      = $this->createAdapter();
        $tableGateway = new TableGateway('TEST', $adapter);
        $rowset       = $tableGateway->select();

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
            $tableGateway = new TableGateway('TEST', $adapter);

            $rowset       = $tableGateway->select();
            $data         = [
                'NAME'  => 'test_name',
                'VALUE' => 'test_value',
            ];
            $affectedRows = $tableGateway->insert($data);
            $this->assertEquals(1, $affectedRows);

            /**
             * @note: this section is not available for OCI8-connection
             * @see: \Laminas\Db\Adapter\Driver\Oci8\Connection::getLastGeneratedValue
             **/
//            $lastInsertId = $tableGateway->getLastInsertValue();
            $lastInsertId = $this->getMaxIdFrom($adapter, 'TEST');
            $rowSet       = $tableGateway->select([
                'ID' => $lastInsertId,
            ]);
            $row          = $rowSet->current();

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
        $sql        = sprintf(
            'select max(%s) as last_id from %s',
            $idFieldName,
            $tableName
        );
        $resultSet  = $connection->execute($sql);
        $row        = $resultSet->current();
        return $row['LAST_ID'];
    }

    /**
     * @dataProvider tableProvider
     * @param string|TableIdentifier|array $table
     */
    public function testTableGatewayWithMetadataFeature($table)
    {
        $getFixedTableIdentifier = function (TableIdentifier $table) {
            $tableName = $table->getTable();
            $tableName = mb_strtoupper($tableName);
            $schema    = $table->getSchema();
            $schema ?: mb_strtoupper($schema);
            return new TableIdentifier($tableName, $schema);
        };

        if (is_string($table)) {
            $table = mb_strtoupper($table);
        } elseif (is_array($table)) {
            foreach ($table as $key => $val) {
                if (is_string($val)) {
                    $table[$key] = mb_strtoupper($val);
                } elseif ($val instanceof TableIdentifier) {
                    $table[$key] = $getFixedTableIdentifier($val);
                }
            }
        } elseif ($table instanceof TableIdentifier) {
            $table = $getFixedTableIdentifier($table);
        }
        $adapter      = $this->createAdapter();
        $tableGateway = new TableGateway($table, $adapter, new MetadataFeature());

        self::assertInstanceOf(TableGateway::class, $tableGateway);
        self::assertSame($table, $tableGateway->getTable());
    }

    /** @psalm-return array<string, array{0: mixed}> */
    public function tableProvider(): array
    {
        return [
            'string'                  => ['test'],
            'aliased string'          => [['foo' => 'test']],
            'TableIdentifier'         => [new TableIdentifier('test')],
            'aliased TableIdentifier' => [['foo' => new TableIdentifier('test')]],
        ];
    }
}
