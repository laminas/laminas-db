<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Mysql;

use Exception;
use Laminas\Db\Adapter\Driver\Pdo\Result as PdoResult;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\Exception\RuntimeException;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    use AdapterTrait;

    /**
     * @psalm-return array<array-key, array{
     *     0: string,
     *     1: mixed[]|array<string, mixed>,
     *     2: array<string, mixed>
     * }>
     */
    public function getQueriesWithRowResult(): array
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
                ['id' => '3', 'name' => '123a', 'value' => 'bar'],
            ],
        ];
    }

    /**
     * @dataProvider getQueriesWithRowResult
     * @covers       \Laminas\Db\Adapter\Adapter::query
     * @covers       \Laminas\Db\ResultSet\ResultSet::current
     */
    public function testQuery(string $query, array $params, array $expected)
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
     * SQL text is: "UPDATE `test` SET `name` = :c_0, `value` = :c_1 WHERE ` id` = :where1"
     * Binding map table
     * -- Bind Index   Bind Name   Field name   Field type
     * -- 0            ":c_0"      "name"       varchar(255)
     * -- 1            ":c_1"      "value"      varchar(255)
     * -- 2            ":where1"   "id"         int
     *
     * @see https://github.com/laminas/laminas-db/issues/47
     * @see https://github.com/laminas/laminas-db/issues/214
     *
     * @return StatementInterface
     */
    protected function getStatementForTestBinding()
    {
        $sql = new Sql($this->adapter);
        /**
         * @type \Laminas\Db\Sql\Update $update
         */
        $update = $sql->update('test');
        $update->set([
            'name'  => ':name',
            'value' => ':value',
        ])->where([
            'id' => ':id',
        ]);
        return $sql->prepareStatementForSqlObject($update);
    }

    /**
     * This test verify exception, if index was confused.
     * Index 0 and 2 is confused.
     */
    public function testBindParamByIndexIsFail()
    {
        $stmt = $this->getStatementForTestBinding();
        try {
            //positional parameters - is invalid
            $stmt->execute([
                1, //    FAIL -- 0         ":c_0"        "name"       varchar(255)
                'foo', //OK   -- 1         ":c_1"        "value"      varchar(255)
                'bar', //FAIL -- 2         ":where1"     "id"         int
            ]);
            $this->assertTrue(false, __METHOD__, "/Fail. Extect exception.");
        } catch (Exception $e) {
            $this->assertTrue(true, __METHOD__, "/Success. We have an exception: " . $e->getMessage());
        }
    }

    /**
     * Expected Result, because bind index is valid
     */
    public function testBindParamByIndexIsSuccess()
    {
        $stmt = $this->getStatementForTestBinding();
        //positional parameters - is valid
        $result = $stmt->execute([
            'bar', //OK -- 0         ":c_0"        "name"       varchar(255)
            'foo', //OK -- 1         ":c_1"        "value"      varchar(255)
            1, //    OK -- 2         ":where1"     "id"         int
        ]);
        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    /**
     * This test verify exception, if names was confused.
     * Names "c_0" and "where1" is confused.
     */
    public function testBindParamByNameIsFail()
    {
        $stmt = $this->getStatementForTestBinding();
        try {
            //"mapped" named parameters
            $stmt->execute([
                'c_0'    => 1, //    FAIL -- 0         ":c_0"        "name"       varchar(255)
                'c_1'    => 'foo', //OK   -- 1         ":c_1"        "value"      varchar(255)
                'where1' => 'bar', //FAIL -- 2         ":where1"     "id"         int
            ]);
            $this->assertTrue(false, __METHOD__, "/Fail. Extect exception.");
        } catch (Exception $e) {
            $this->assertTrue(true, __METHOD__, "/Success. We have an exception: " . $e->getMessage());
        }
    }

    /**
     * Expected Result, because bind names is valid
     */
    public function testBindParamByNameIsSuccess()
    {
        $stmt = $this->getStatementForTestBinding();
        //"mapped" named parameters
        $result = $stmt->execute([
            'c_0'    => 'bar', //OK -- 0         ":c_0"        "name"       varchar(255)
            'c_1'    => 'foo', //OK -- 1         ":c_1"        "value"      varchar(255)
            'where1' => 1, //    OK -- 2         ":where1"     "id"         int
        ]);
        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    /**
     * This test verify exception, if field names was confused.
     * Field name "id" named "idFieldName" - it is wrong.
     */
    public function testBindParamByFieldNameIsFail()
    {
        $stmt = $this->getStatementForTestBinding();
        try {
            //real named parameters
            $stmt->execute([
                'name'  => 'bar', //   OK   -- 0         ":c_0"        "name"       varchar(255)
                'value' => 'foo', //   OK   -- 1         ":c_1"        "value"      varchar(255)
                'idFieldName' => 1, // FAIL -- 2         ":where1"     "id"         int
            ]);
            $this->assertTrue(false, __METHOD__, "/Fail. Extect exception.");
        } catch (Exception $e) {
            $this->assertTrue(true, __METHOD__, "/Success. We have an exception: " . $e->getMessage());
        }
    }

    /**
     * Expected Result, because bind filed names is valid
     */
    public function testBindParamByFieldNameIsSuccess()
    {
        $stmt = $this->getStatementForTestBinding();
        //real named parameters
        $result = $stmt->execute([
            'name'  => 'bar', //OK -- 0         ":c_0"        "name"       varchar(255)
            'value' => 'foo', //OK -- 1         ":c_1"        "value"      varchar(255)
            'id'    => 1, //    OK -- 2         ":where1"     "id"         int
        ]);
        $this->assertInstanceOf(ResultInterface::class, $result);
    }
}
