DROP TABLE IF EXISTS test;
CREATE TABLE test (
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

DROP TABLE IF EXISTS test_charset;
CREATE TABLE test_charset (
    id INT NOT NULL AUTO_INCREMENT,
    field$ VARCHAR(255) NOT NULL,
    field_ VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO test_charset (field$, field_) VALUES
('foo', 'bar'),
('bar', 'baz');

DROP TABLE IF EXISTS test_audit_trail;
CREATE TABLE test_audit_trail (
    id INT NOT NULL AUTO_INCREMENT,
    test_id INT NOT NULL,
    test_value_old VARCHAR(255) NOT NULL,
    test_value_new VARCHAR(255) NOT NULL,
    changed TIMESTAMP,
    PRIMARY KEY (id)
);

DROP VIEW IF EXISTS test_view;
CREATE VIEW test_view
AS
SELECT
    name AS v_name,
    value AS v_value
FROM
    test;

DROP TRIGGER IF EXISTS after_test_update;
CREATE TRIGGER after_test_update
    AFTER UPDATE ON test
    FOR EACH ROW
    INSERT INTO test_audit_trail
    SET
        test_id = OLD.id,
        test_value_old = OLD.value,
        test_value_new = NEW.value,
        changed = NOW();
