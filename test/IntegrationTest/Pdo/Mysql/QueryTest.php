<?php

namespace ZendTest\Db\IntegrationTest\Pdo\Mysql;

use PHPUnit\Framework\TestCase;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\Pdo\Result as PdoResult;
use Zend\Db\ResultSet\ResultSet;

class QueryTest extends TestCase
{
    use ConnectionTrait;

    public function getQueriesWithRowResult()
    {
        return [
            ['SELECT * FROM test WHERE id = ?', [1], ['id' => 1, 'name' => 'foo', 'value' => 'bar']],
            ['SELECT * FROM test WHERE id = :id', [':id' => 1], ['id' => 1, 'name' => 'foo', 'value' => 'bar']],
            ['SELECT * FROM test WHERE id = :id', ['id' => 1], ['id' => 1, 'name' => 'foo', 'value' => 'bar']]
        ];
    }

    /**
     * @dataProvider getQueriesWithRowResult
     */
    public function testQuery($query, $params, $expected)
    {
        $result = $this->adapter->query($query, $params);
        $this->assertInstanceOf(ResultSet::class, $result);
        $current = $result->current();
        // test as array value
        $this->assertEquals($expected, (array) $current);
        // test as object value
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $current->$key);
        }
    }

    /**
     * @see https://github.com/zendframework/zend-db/issues/288
     */
    public function testSetSessionTimeZone()
    {
        $result = $this->adapter->query('SET @@session.time_zone = :tz', [':tz' => 'SYSTEM']);
        $this->assertInstanceOf(PdoResult::class, $result);
    }
}
