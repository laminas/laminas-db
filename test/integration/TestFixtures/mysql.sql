CREATE TABLE IF NOT EXISTS test (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    value VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO test (name, value) VALUES
('foo', 'bar'),
('bar', 'baz'),
('123a', 'bar'),
('123', 'bar');

CREATE TABLE IF NOT EXISTS test_charset (
    id INT NOT NULL AUTO_INCREMENT,
    field$ VARCHAR(255) NOT NULL,
    field_ VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO test_charset (field$, field_) VALUES
('foo', 'bar'),
('bar', 'baz');

CREATE TABLE test_audit_trail (
    id INT NOT NULL AUTO_INCREMENT,
    test_id INT NOT NULL,
    test_value_old VARCHAR(255) NOT NULL,
    test_value_new VARCHAR(255) NOT NULL,
    changed TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE VIEW test_view
AS
SELECT
    name AS v_name,
    value AS v_value
FROM
    test;

CREATE TRIGGER after_test_update
    AFTER UPDATE ON test
    FOR EACH ROW
    INSERT INTO test_audut_trail
    SET
        test_id = OLD.id,
        test_value_old = OLD.value,
        test_value_new = NEW.value,
        changed = NOW();
