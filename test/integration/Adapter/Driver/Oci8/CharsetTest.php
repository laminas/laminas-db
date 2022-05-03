<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

use function sprintf;

class CharsetTest extends TestCase
{
    use TraitSetup;

    /**
     * @covers \Laminas\Db\TableGateway\TableGateway::__construct
     */
    public function testConstructor()
    {
        $adapter      = $this->createAdapterWithQuoteIdentifiers();
        $tableGateway = new TableGateway('test_charset', $adapter);
        $this->assertInstanceOf(TableGateway::class, $tableGateway);
    }

    public function testSelectWithAdapterDirectSql()
    {
        $adapter = $this->createAdapterWithQuoteIdentifiers();

        $expectedSql = 'select id, field$, field_, field# from test_charset';

        $statement = $adapter->createStatement($expectedSql);

        $actualSql = $statement->getSql();
        $this->assertEquals($expectedSql, $actualSql);

        $result = $statement->execute();
        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    public function testSelectQuoteFieldName()
    {
        $adapter = $this->createAdapterWithQuoteIdentifiers();
        $select  = new Select('TEST_CHARSET');
        $select->columns([
            'FIELD$',
            'FIELD_',
            'FIELD#',
        ]);
        $plarform  = $adapter->getPlatform();
        $sqlString = $select->getSqlString($plarform);
        $statement = $adapter->createStatement($sqlString);
        $result    = $statement->execute();
        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    /**
     * @see https://github.com/zendframework/zend-db/issues/35
     * @see https://github.com/zendframework/zend-db/pull/178
     *
     * @return mixed
     */
    public function testInsertWithTableGateway()
    {
        $adapter      = $this->createAdapterWithQuoteIdentifiers();
        $tableGateway = new TableGateway('TEST_CHARSET', $adapter);

        $affectedRows = $tableGateway->insert([
            'FIELD$' => 'field$value',
            'FIELD_' => 'field_value',
            'FIELD#' => 'field#value',
        ]);
        $this->assertEquals(1, $affectedRows);

        //$result = $tableGateway->getLastInsertValue(); // Is not avaliable.
        return $this->getMaxIdFrom($adapter, 'TEST_CHARSET');
    }

    /**
     * @param string $idFieldName
     * @return mixed
     */
    public function getMaxIdFrom(
        Adapter $adapter,
        string $tableName,
        $idFieldName = 'ID'
    ) {
        /**
         * @warning:  "$adapter->query" does not work in the same transaction.
         */
        //$resultSet = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        //$resultArr = $resultSet->toArray();
        //$result = $resultArr[0]['LAST_ID'];

        $connection = $adapter->getDriver()->getConnection();
        $sql        = sprintf(
            'select max(%s) as last_id from %s',
            $idFieldName,
            $tableName
        );
        $resultSet  = $connection->execute($sql);
        $row        = $resultSet->current();
        return $row['LAST_ID'];
    }

    /**
     * @depends testInsertWithExtendedCharsetFieldName
     * @param mixed $id
     */
    public function testUpdateWithTableGateway($id)
    {
        $adapter      = $this->createAdapterWithQuoteIdentifiers();
        $tableGateway = new TableGateway('test_charset', $adapter);

        $data         = [
            'FIELD$' => 'test$alue3',
            'FIELD_' => 'test_value4',
            'FIELD#' => 'test#value4',
        ];
        $affectedRows = $tableGateway->update($data, ['id' => $id]);
        $this->assertEquals(1, $affectedRows);

        $rowSet = $tableGateway->select(['id' => $id]);
        $row    = $rowSet->current();

        foreach ($data as $key => $value) {
            $this->assertEquals($row->$key, $value);
        }
    }
}
