# laminas-db

[![Build Status](https://github.com/laminas/laminas-db/workflows/Continuous%20Integration/badge.svg)](https://github.com/laminas/laminas-db/actions?query=workflow%3A"Continuous+Integration")

`Laminas\Db` is a component that abstract the access to a Database using an object
oriented API to build the queries. `Laminas\Db` consumes different storage adapters
to access different database vendors such as MySQL, PostgreSQL, Oracle, IBM DB2,
Microsoft Sql Server, PDO, etc.

## Contributing

Please be sure to read the [contributor's guide](/laminas/.github/blob/main/CONTRIBUTING.md) for general information on contributing.
This section outlines specifics for laminas-db.

### Test suites

The `phpunit.xml.dist` file defines two test suites, "unit test" and "integration test".
You can run one or the other using the `--testsuite` option to `phpunit`:

```bash
$ ./vendor/bin/phpunit --testsuite "unit test" # unit tests only
$ ./vendor/bin/phpunit --testsuite "integration test" # integration tests only
```

Unit tests do not require additional functionality beyond having the appropriate database extensions present and loaded in your PHP binary.

### Integration tests

To run the integration tests, you need databases.
The repository includes a `docker-compose.yml` which allows you to fire up [Docker](https://www.docker.com) containers with several of our target databases, including:

- MySQL
- PostgreSQL
- SQL Server

To start the Docker containers and wait till the containers are ready:

```bash
$ docker-compose up -d
```

Set the following environment variables to run the integration test for that platform:

- TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL=true
- TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV=true
- TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL=true
- TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLITE_MEMORY=true

On Linux:

```bash
export TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL=true
```

On Windows (cmd):

```
set TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL=true
```

From there, you can run the integration tests:

```bash
$ ./vendor/bin/phpunit --testsuite "integration test"
```

-----

- File issues at https://github.com/laminas/laminas-db/issues
- Documentation is at https://docs.laminas.dev/laminas-db/
