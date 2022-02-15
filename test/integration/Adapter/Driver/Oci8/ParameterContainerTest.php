<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Oci8;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\Oci8\Result;
use Laminas\Db\Adapter\ParameterContainer;
use PHPUnit\Framework\TestCase;

class ParameterContainerTest extends TestCase
{
    use TraitSetup;

    /**
     * Test out parameter from procedure.
     *
     * Expect, bind param from reference.
     *
     * @see https://discourse.laminas.dev/t/how-to-use-parameters-by-reference-witch-laminas-db-adapter/1993
     *
     * <code classs="pl-sql">
     * set serveroutput on;
     * declare
     *   out_result number;
     * begin
     *   ldbt$test.incOutProcedure(
     *       p_val        => 5,
     *       p_out_result => out_result
     *   );
     *   dbms_output.put_line('Result is '||out_result);
     * end;
     * -- Result is 6
     * -- PL/SQL procedure successfully completed.
     * </code>
     */
    public function testIncOutOnProcedure()
    {
        $this->markTestIncomplete(
            '@TODO: enable test after fix binding ParameterContainer value by reference'
        );
        return;

        $adapter = $this->createAdapter();
        try {
            $sqlString          = '
declare
begin
  ldbt$test.incOutProcedure(
      p_val        => :bind_p_val,
      p_out_result => :bind_p_out_result
  );
end;
            ';
            $parameterContainer = new ParameterContainer([
                'bind_p_val'        => 5,
                'bind_p_out_result' => null,
            ]);

            $statement = $adapter->createStatement($sqlString, $parameterContainer);

            //$statement = $adapter->query($sqlString, Adapter::QUERY_MODE_PREPARE);
            //$statement->setParameterContainer($parameterContainer);

            /**
             * @type Result $result
             */
            $result = $statement->execute();
            $actual = $parameterContainer->offsetGet('bind_p_out_result');
            $this->assertSame('6', $actual);
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }

    /**
     * Test out parameters from function.
     */
    public function testIncOutOnFunction()
    {
        $this->markTestIncomplete(
            '@TODO: enable test after fix binding ParameterContainer value by reference'
        );
        return;

        $adapter = $this->createAdapter();
        try {
            $sqlString          = '
declare
begin
  :res := ldbt$test.incOutFunction(
      p_val        => :bind_p_val,
      p_out_result => :bind_p_out_result
  );
end;';
            $parameterContainer = new ParameterContainer([
                'bind_p_val'        => 5,
                'bind_p_out_result' => null,
                'res'               => null,
            ]);

            $statement = $adapter->createStatement($sqlString, $parameterContainer);

            //$statement = $adapter->query($sqlString, Adapter::QUERY_MODE_PREPARE);
            //$statement->setParameterContainer($parameterContainer);

            /**
             * @type Result $result
             */
            $result = $statement->execute();

            $res        = $parameterContainer->offsetGet('res');
            $bindResult = $parameterContainer->offsetGet('bind_p_out_result');
            $this->assertSame('6', $res);
            $this->assertSame($res, $bindResult);
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }

    public function testPipelinedAdapterQuery()
    {
        $adapter  = $this->createAdapter();
        $expected = [
            0 => [
                'ID'    => '1',
                'NAME'  => 'foo',
                'VALUE' => 'bar',
            ],
        ];
        try {
            $selectString       = '
            select *
            from table(ldbt$test.test_table_search( 
                p_id => :p_id_bind,  
                p_name => :p_name_bind
            ))
            ';
            $parameterContainer = new ParameterContainer([
                'p_id_bind'   => 1,
                'p_name_bind' => '%',
            ]);

            // 1. One line execute
            $resultSet = $adapter->query($selectString, $parameterContainer);
            $actual    = $resultSet->toArray();
            $this->assertSame($expected, $actual);
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
            $this->assertSame($expected, $actual);
        } finally {
            $adapter->getDriver()->getConnection()->disconnect();
        }
    }
}
