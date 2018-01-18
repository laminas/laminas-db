<?php

namespace ZendTest\Db\IntegrationTest\Pdo\Mysql;

use PHPUnit\Framework\TestCase;
use Zend\Db\TableGateway\TableGateway;

class TableGatewayTest extends TestCase
{
    use ConnectionTrait;

    public function testConstructor()
    {
        $tableGateway = new TableGateway('test', $this->adapter);
        $this->assertInstanceOf(TableGateway::class, $tableGateway);
    }

    public function testSelect()
    {
        $tableGateway = new TableGateway('test', $this->adapter);
        $rowset = $tableGateway->select();

        $this->assertTrue(count($rowset) > 0);
        foreach ($rowset as $row) {
            $this->assertTrue(isset($row->id));
            $this->assertNotEmpty(isset($row->name));
            $this->assertNotEmpty(isset($row->value));
        }
    }

    public function testInsert()
    {
        $tableGateway = new TableGateway('test', $this->adapter);

        $rowset = $tableGateway->select();
        $prevTot = count($rowset);

        $affectedRows = $tableGateway->insert([
            'name'  => 'test_name',
            'value' => 'test_value'
        ]);
        $this->assertEquals(1, $affectedRows);

        $rowset = $tableGateway->select();
        $this->assertEquals($prevTot + 1, count($rowset));
    }

    /**
     * @see https://github.com/zendframework/zend-db/issues/35
     * @see https://github.com/zendframework/zend-db/pull/178
     */
    public function testInsertWithExtendedCharsetFieldName()
    {
        $tableGateway = new TableGateway('test_charset', $this->adapter);

        $affectedRows = $tableGateway->insert([
            'field$' => 'test_value1',
            'field_' => 'test_value2'
        ]);
        $this->assertEquals(1, $affectedRows);
    }
}
