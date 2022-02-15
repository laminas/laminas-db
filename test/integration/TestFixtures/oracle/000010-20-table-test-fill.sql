INSERT INTO test (name, value)VALUES ('foo', 'bar')
;^
INSERT INTO test (name, value)VALUES ('bar', 'baz')
;^
INSERT INTO test (name, value)VALUES ('123a', 'bar')
;^
INSERT INTO test (name, value)VALUES ('123', 'bar')
;^
