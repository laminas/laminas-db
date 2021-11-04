CREATE OR ALTER TRIGGER after_test_update ON test
    AFTER UPDATE
    AS
BEGIN
    INSERT INTO test_audit_trail(test_id, test_value_old, test_value_new, changed)
    SELECT
        id,
        value,
        inserted.value,
        GETDATE()
    FROM inserted
END;
