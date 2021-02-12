CREATE TRIGGER after_test_update ON test
    AFTER UPDATE
    AS
BEGIN
    INSERT INTO test_audut_trail(test_id, test_value_old, test_value_new, changed)
    SELECT
        id,
        value,
        inserted.value,
        GETDATE()
    FROM inserted
END;