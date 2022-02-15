<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Oracle;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;
use PHPUnit\Framework\TestCase;

class SqlTest extends TestCase
{
    use AdapterTrait;

    /**
     * Expect 4 records. @see "test/integration/TestFixtures/oracle/000010-20-table-test-fill.sql"
     */
    public function testCount()
    {
        $adapter = $this->createAdapter();
        try {
            $sql             = new Sql($adapter);
            $tableIdentifier = new TableIdentifier('test');
            $select          = $sql->select($tableIdentifier);
//        $select->from([
//            't' => $tableIdentifier
//        ]);
            $select->columns([
                'cntRows' => new Expression('count(*)'),
            ]);
            $selectString = $sql->buildSqlString($select);
            /**
             * @type ResultSet $resultSet
             */
            $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $rows      = $resultSet->toArray();
            $rowsCount = $rows[0]['CNTROWS'];
            $this->assertSame('4', $rowsCount, 'Invalid count rows in a table "test"');
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }
}
