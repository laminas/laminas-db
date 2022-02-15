<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Exception;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;
use PHPUnit\Framework\TestCase;

class SqlTest extends TestCase
{
    use TraitSetup;

    /**
     * This test show Oracle feature - case depended table name.
     */
    public function testSelectFromQuoteIdentifiersFailOra00942()
    {
        $adapter = $this->createAdapterWithQuoteIdentifiers();
        $actual  = '';
        try {
            $sql = new Sql($adapter);

            $select = new Select();
            $select->from([
                't' => 'test',
            ]);

            $select->columns([
                'cntRows' => new Expression('count(*)'),
            ]);
            $selectString = $sql->buildSqlString($select);
            /**
             * @type ResultSet $resultSet
             */
            $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        } catch (Exception $e) {
            $actual = $e->getMessage();
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
        $expect = 'ORA-00942: table or view does not exist';
        $this->assertStringContainsString($expect, $actual);
    }

    /**
     * This test show Oracle feature - case depended table name.
     */
    public function testSelectFromQuoteIdentifiersSuccess()
    {
        $adapter = $this->createAdapterWithQuoteIdentifiers();
        $actual  = '';
        try {
            $sql = new Sql($adapter);

            $select = new Select();
            $select->from([
                't' => 'TEST',
            ]);

            $select->columns([
                'cntRows' => new Expression('count(*)'),
            ]);
            $selectString = $sql->buildSqlString($select);
            /**
             * @type ResultSet $actual
             */
            $actual = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $this->assertInstanceOf(ResultSet::class, $actual);
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }

    public function testSelectWithoutQuoteIdentifiers()
    {
        $adapter = $this->createAdapterWithoutQuoteIdentifiers();
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
            $select          = $sql->select();
            $tableIdentifier = new TableIdentifier('test');
            $select->from([
                't' => $tableIdentifier,
            ]);

            $select->columns([
                'cntRows' => new Expression('count(*)'),
            ]);
            $selectString = $sql->buildSqlString($select);
            /**
             * @type ResultSet $resultSet
             */
            $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $actual    = $resultSet->toArray();
            $expected  = [
                0 => [
                    'CNTROWS' => '4',
                ],
            ];
            $this->assertSame($expected, $actual);
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }
}
