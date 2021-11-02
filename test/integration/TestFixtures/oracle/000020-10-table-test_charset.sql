BEGIN
    EXECUTE IMMEDIATE 'drop sequence sq_test_charset_id';
EXCEPTION
    WHEN OTHERS THEN
        -- OCIStmtExecute: ORA-02289: sequence does not exist
        IF SQLCODE != -02289 THEN
            RAISE;
        END IF;
END;
;^
create sequence sq_test_charset_id start with 1 increment by 1 nomaxvalue nocache
;^
BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE test_charset';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -942 THEN
            RAISE;
        END IF;
END;
;^
CREATE TABLE test_charset
(
    id    number(10)
        constraint pk_test_charset PRIMARY KEY,
    field$  varchar2(255)
        constraint nn_test_charset_field$ NOT NULL,
    field_ varchar2(255)
        constraint nn_test_charset_field_ NOT NULL
)
;^
comment on table test_charset is 'Test table "test_charset" for laminas-db'
;^
comment on column test_charset.id is 'Primary Key of table "test_charset"'
;^
create or replace trigger tg_test_charset_bi
    before insert
    on     test_charset
    for    each row
begin
    if :new.id is null then
        :new.id := sq_test_charset_id.nextval;
    end if;
end;
;^