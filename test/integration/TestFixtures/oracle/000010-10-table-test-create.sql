BEGIN
    EXECUTE IMMEDIATE 'drop sequence sq_test_id';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -942 THEN
            RAISE;
        END IF;
END;
;^
create sequence sq_test_id start with 1 increment by 1 nomaxvalue nocache
;^
BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE test';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -942 THEN
            RAISE;
        END IF;
END;
;^
CREATE TABLE test
(
    id    number(10)
        constraint pk_test PRIMARY KEY,
    name  varchar2(100)
        constraint nn_test_name NOT NULL,
    value varchar2(100)
        constraint nn_test_value NOT NULL
)
;^
comment on table test is 'Test table for laminas-db'
;^
comment on column test.id is 'Primary Key of table "test"'
;^
create or replace trigger tg_test_bi
    before insert
    on     test
    for    each row
begin
    if :new.id is null then
        :new.id := sq_test_id.nextval;
    end if;
end;
;^