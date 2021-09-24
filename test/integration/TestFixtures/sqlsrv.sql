DROP TABLE IF EXISTS test;
CREATE TABLE test (
                      id INT NOT NULL IDENTITY,
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
                              id INT NOT NULL IDENTITY,
                              field$ VARCHAR(255) NOT NULL,
                              field_ VARCHAR(255) NOT NULL,
                              PRIMARY KEY (id)
);

INSERT INTO test_charset (field$, field_) VALUES
('foo', 'bar'),
('bar', 'baz');

DROP TABLE IF EXISTS test_audit_trail
CREATE TABLE test_audit_trail (
                                  id INT NOT NULL IDENTITY,
                                  test_id INT NOT NULL,
                                  test_value_old VARCHAR(255) NOT NULL,
                                  test_value_new VARCHAR(255) NOT NULL,
                                  changed DATETIME2(0),
                                  PRIMARY KEY (id)
);