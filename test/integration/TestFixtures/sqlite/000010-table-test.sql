DROP TABLE IF EXISTS test;
CREATE TABLE test
(
    id    INTEGER PRIMARY KEY AUTOINCREMENT,
    name  TEXT NOT NULL,
    value TEXT NOT NULL
);

INSERT INTO test (name, value)
VALUES ('foo', 'bar'),
       ('bar', 'baz'),
       ('123a', 'bar'),
       ('123', 'bar');
