create or replace package ldbt$test is
    /**
     Increment value p_val and return result across out parameter
     */
    procedure incOutProcedure(
        p_val            number,           -- value for inccrement
        p_inc_factor     number default 1, -- increment step
        p_out_result out number            -- result
    );

    /**
      1. Increment value p_val and return result across out parameter
      2. Return p_val as result
     */
    function incOutFunction(
        p_val            number,           -- value for inccrement
        p_inc_factor     number default 1, -- increment step
        p_out_result out number            -- result
    ) return number;

    /**

     */
    type type_test_table_record  is record(
        id     test.id%type,     -- field "id" - PK of record
        name   test.name%type,   -- field "name"
        value  test.value%type   -- field "value"
    );
    type type_table_test is table of type_test_table_record;
    /**
      Do search in table "test".
      Returns table of record.
     */
    function test_table_search(
        p_id     test.id%type default null,    -- Search by "id"
        p_name   test.name%type default null,  -- Search by "name"
        p_value  test.value%type default null  -- Search by "value"
    ) return type_table_test  parallel_enable pipelined;
end ldbt$test;