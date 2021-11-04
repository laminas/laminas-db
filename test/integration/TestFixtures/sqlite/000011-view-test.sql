DROP VIEW IF EXISTS v$test;
CREATE VIEW v$test(v_id, v_name, v_value)
AS
SELECT id, name, test.value
FROM TEST
;
