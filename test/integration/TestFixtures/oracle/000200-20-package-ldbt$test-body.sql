create or replace package body ldbt$test is
    function test_table_search(
        p_id     test.id%type default null,    -- Search by "id"
        p_name   test.name%type default null,  -- Search by "name"
        p_value   test.value%type default null -- Search by "value"
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
    end;

end ldbt$test;