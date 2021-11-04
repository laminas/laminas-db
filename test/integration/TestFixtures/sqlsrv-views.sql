CREATE OR ALTER VIEW test_view
AS (
SELECT
    name AS v_name,
    value AS v_value
FROM
    test
);