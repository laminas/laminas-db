<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\Oci8\Result;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 * @group integration-oracle
 */
class AdapterTest extends TestCase
{
    use TraitSetup;

    public function testQuerySelectCount()
    {
        $adapter = $this->createAdapterWithoutQuoteIdentifiers();
        try {
            $resultSet = $adapter->query(
                'select count(1) as cntRows from test t where 1 = -1',
                Adapter::QUERY_MODE_EXECUTE
            );
            $resultArray = $resultSet->toArray();
            $result = $resultArray[0]['CNTROWS'];
            $this->assertEquals('0', $result);
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }

    public function testQuerySelectCountId0Named()
    {
        $adapter = $this->createAdapter();
        try {
            /**
             * var StatementInterface $statement
             */
            $statement = $adapter->query(
                'select count(1) as cntRows from test t where id = :id',
                Adapter::QUERY_MODE_PREPARE
            );
            $parameterContainer = new ParameterContainer(['id' => 0]);
            $statement->setParameterContainer($parameterContainer);
            /**
             * @var $result ResultInterface|Result
             */
            $result = $statement->execute();
            $row = $result->current();
            $result = $row['CNTROWS'];
            $this->assertEquals('0', $result);
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }


    /*
     * @note: OCI8 does not support positional parameters
     */
    public function testQuerySelectCountId0Positional()
    {
        $adapter = $this->createAdapter();
        $actual = '';
        try {
            $resultSet = $adapter->query('SELECT count(*) as cntRows FROM test WHERE id = ?', [0]);
        } catch (\Exception $e) {
            $actual = $e->getMessage();
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
        $expect = 'oci_bind_by_name(): ORA-01036: illegal variable name/number';
        $this->assertStringContainsString($expect, $actual);
    }
}
