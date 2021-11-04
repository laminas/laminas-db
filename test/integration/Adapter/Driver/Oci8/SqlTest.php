<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;
use PHPUnit\Framework\TestCase;

class SqlTest extends TestCase
{
    use TraitSetup;

    public function testSelectFromQuoteIdentifiersOraException()
    {
        $adapter = $this->createAdapterWithoutQuoteIdentifiers();
        $actual = '';
        try {

            $sql = new Sql($adapter);

            $select = new Select();
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
        } catch (\Exception $e) {
            $actual = $e->getMessage();
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
        $expect = 'ORA-00942: table or view does not exist';
        $this->assertStringContainsString($expect, $actual);
    }

    public function testSelectFromQuoteIdentifiers()
    {
        $adapter = $this->createAdapter();
        try {

            $sql = new Sql($adapter);

//1. Simple
//            $select = new Select();
//            $select->from([
//                't' => 'test'
//            ]);

//2. By "TableIdentifier" in constructor
//            $tableIdentifier = new TableIdentifier('test');
//            $select = $sql->select($tableIdentifier);

// 3. By "TableIdentifier" across method "from",
            $select = $sql->select();
            $tableIdentifier = new TableIdentifier('test');
            $select->from([
                't' => $tableIdentifier
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
            $rowsCount = $rows[0]['CNTROWS'];
            $this->assertSame('4', $rowsCount, 'Invalid count rows in a table "test"');
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }
}
