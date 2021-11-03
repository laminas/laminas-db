create or replace package body ldbt$test is
    procedure incOutProcedure(
        p_val            number,           -- value for inccrement
        p_inc_factor     number default 1, -- increment step
        p_out_result out number            -- result
    )
    is
    begin
        p_out_result := p_val + p_inc_factor;
    end incOutProcedure;

    function incOutFunction(
        p_val            number,           -- value for inccrement
        p_inc_factor     number default 1, -- increment step
        p_out_result out number            -- result
    ) return number
    is
    begin
        p_out_result := p_val + p_inc_factor;
        return  p_out_result;
    end incOutFunction;

    function test_table_search(
        p_id     test.id%type default null,    -- Search by "id"
        p_name   test.name%type default null,  -- Search by "name"
        p_value  test.value%type default null  -- Search by "value"
    ) return type_table_test  parallel_enable pipelined
        is
        result_record type_table_test;
    begin
        select unique
            id,
            name ,
            value
            bulk collect into result_record
        from test t
        where (p_id is null or t.id = p_id)
          and (p_name is null or t.name like p_name)
          and (p_value is null or t.value like p_value)
        ;

        for i in 1..sql%rowcount loop
                pipe row(result_record(i));
            end loop;
    end test_table_search;

end ldbt$test;