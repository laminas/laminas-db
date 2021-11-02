<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\Oci8\Result;
use Laminas\Db\Adapter\ParameterContainer;
use PHPUnit\Framework\TestCase;

class ParameterContainerTest extends TestCase
{
    use TraitSetup;

    public function testPipelinedAdapterQuery()
    {
        $adapter = $this->createAdapter();
        $expectedSearchById_1 = [
            0 => [
                'ID' => '1',
                'NAME' => 'foo',
                'VALUE' => 'bar',
            ]
        ];
        try {
            $selectString = '
            select *
            from table(ldbt$test.test_table_search( 
                p_id => :p_id_bind,  
                p_name => :p_name_bind
            ))
            ';
            $parameterContainer = new ParameterContainer([
                'p_id_bind' => 1,
                'p_name_bind' => '%'
            ]);

            // 1. One line execute
            $resultSet = $adapter->query($selectString, $parameterContainer);
            $actual = $resultSet->toArray();
            $this->assertSame($expectedSearchById_1, $actual);
            unset($resultSet, $actual);

            // 2. Chain: prepare >> setParameterContainer >> execute
            $statement = $adapter->query($selectString, Adapter::QUERY_MODE_PREPARE);
            $statement->setParameterContainer($parameterContainer);
            /**
             * @type Result $result
             */
            $result = $statement->execute();
            $actual = [$result->current()];
            while ($row = $result->next()) {
                $actual[] = $row;
            }
            $this->assertSame($expectedSearchById_1, $actual);

        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }
    /**
     * @TODO: find laminas-db tool for table-expression
     **/
//    public function testPipelinedWithTableIdentifier()
//    {
//        $ti = new TableIdentifier([
//            't' => new Expression('table(ldbt$test.test_table_search(p_id => :p_id_bind,  p_name => :p_name_bind ))')
//        ]);
//    }
}
