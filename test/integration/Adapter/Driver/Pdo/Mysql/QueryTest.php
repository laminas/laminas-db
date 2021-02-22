<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Mysql;

use Laminas\Db\Adapter\Driver\Pdo\Result as PdoResult;
use Laminas\Db\Adapter\Exception\RuntimeException;
use Laminas\Db\ResultSet\ResultSet;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    use AdapterTrait;

    public function getQueriesWithRowResult()
    {
        return [
            ['SELECT * FROM test WHERE id = ?', [1], ['id' => 1, 'name' => 'foo', 'value' => 'bar']],
            ['SELECT * FROM test WHERE id = :id', [':id' => 1], ['id' => 1, 'name' => 'foo', 'value' => 'bar']],
            ['SELECT * FROM test WHERE id = :id', ['id' => 1], ['id' => 1, 'name' => 'foo', 'value' => 'bar']],
            ['SELECT * FROM test WHERE name = ?', ['123'], ['id' => '4', 'name' => '123', 'value' => 'bar']],
            [
                // name is string, but given parameter is int, can lead to unexpected result
                'SELECT * FROM test WHERE name = ?',
                [123],
                ['id' => '3', 'name' => '123a', 'value' => 'bar']
            ],
        ];
    }

    /**
     * @dataProvider getQueriesWithRowResult
     * @covers \Laminas\Db\Adapter\Adapter::query
     * @covers \Laminas\Db\ResultSet\ResultSet::current
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

    public function testSelectWithNotPermittedBindParamName()
    {
        $this->expectException(RuntimeException::class);
        $this->adapter->query('SET @@session.time_zone = :tz$', [':tz$' => 'SYSTEM']);
    }

    /**
     * @see https://github.com/laminas/laminas-db/issues/47
     */
    public function testNamedParameters()
    {
        $sql = new \Laminas\Db\Sql\Sql($this->adapter);

        $insert = $sql->update('test');
        $insert->set([
            'name'  => ':name',
            'value' => ':value'
        ])->where(['id' => ':id']);
        $stmt = $sql->prepareStatementForSqlObject($insert);

        //positional parameters
        $stmt->execute([
            1,
            'foo',
            'bar'
        ]);

        //"mapped" named parameters
        $stmt->execute([
            'c_0'    => 1,
            'c_1'    => 'foo',
            'where1' => 'bar'
        ]);

        //real named parameters
        $stmt->execute([
            'id'    => 1,
            'name'  => 'foo',
            'value' => 'bar'
        ]);
    }
}
