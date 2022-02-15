<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Oracle;

use PHPUnit\Framework\TestCase;

class AdapterTest extends TestCase
{
    use AdapterTrait;

    public function testQuerySelectCountRawSql()
    {
        $adapter = $this->createAdapter();
        try {
            $selectString = 'select count(1) as cnt from test';
            $resultSet    = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $rows         = $resultSet->toArray();
            $rowsCount    = $rows[0]['CNT'];
            $this->assertSame('4', $rowsCount, 'Invalid count rows in a table "test"');
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }

    public function testQuerySelectCountId0Positional()
    {
        $adapter = $this->createAdapter();
        try {
            $resultSet = $adapter->query('SELECT count(*) as cnt FROM test WHERE id = ?', [0]);
            $rowData   = $resultSet->current()->getArrayCopy();
            $result    = $rowData['CNT'];
            $this->assertSame('0', $result, 'Invalid count rows in a table "test"');
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }
}
