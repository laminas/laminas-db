create or replace package ldbt$test is
  type type_test_table_record  is record(
    id     test.id%type,             --field "id" - PK of record
    name   test.name%type,           --field "name"
    value  test.value%type           --field "value"
  );
  type type_table_test is table of type_test_table_record;

  function test_table_search(
    p_id     test.id%type default null,    -- Search by "id"
    p_name   test.name%type default null,  -- Search by "name"
    p_value   test.value%type default null -- Search by "value"
  ) return type_table_test  parallel_enable pipelined;
end ldbt$test;