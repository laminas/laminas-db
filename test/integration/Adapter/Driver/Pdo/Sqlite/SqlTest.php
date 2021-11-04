<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Sqlite;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

use function count;

class SqlTest extends TestCase
{
    use AdapterTrait;

    /**
     * Expect 4 records. @see "test/integration/TestFixtures/sqlite.sql"
     */
    public function testCount()
    {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from([
            't' => 'test'
        ]);
        $select->columns([
            'cntRows' => new Expression('count(*)')
        ]);
        $selectString = $sql->buildSqlString($select);
        /**
         * @type ResultSet $resultSet
         */
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $rows = $resultSet->toArray();
        $rowsCount = $rows[0]['cntRows'];
        $rowsCount = intval($rowsCount);
        $this->assertSame(4, $rowsCount, 'Invalid count rows in a table "test"');
    }
}
