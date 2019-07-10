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
