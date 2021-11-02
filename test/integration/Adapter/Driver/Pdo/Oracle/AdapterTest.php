<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Oracle;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\Feature\MetadataFeature;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

use function count;

class AdapterTest extends TestCase
{
    use AdapterTrait;

    public function testQuerySelectCountRawSql()
    {
        $adapter = $this->createAdapter();
        try {
            $selectString = 'select count(1) as cnt from test';
            $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $rows = $resultSet->toArray();
            $rowsCount = $rows[0]['CNT'];
            $this->assertSame('4', $rowsCount, 'Invalid count rows in a table "test"');
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }

    /**
     *
     */
    public function testQuerySelectCountId0Positional()
    {
        $adapter = $this->createAdapter();
        try {
            $resultSet = $adapter->query('SELECT count(*) as cnt FROM test WHERE id = ?', [0]);
            $rowData = $resultSet->current()->getArrayCopy();
            $result = $rowData['CNT'];
            $this->assertSame('0', $result, 'Invalid count rows in a table "test"');
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }
}
