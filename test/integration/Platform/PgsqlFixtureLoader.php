<?php

namespace LaminasIntegrationTest\Db\Platform;

use Exception;
use PDO;

use function file_get_contents;
use function getenv;
use function print_r;
use function sprintf;

class PgsqlFixtureLoader implements FixtureLoader
{
    /** @var string */
    private $fixtureFile = __DIR__ . '/../TestFixtures/pgsql.sql';

    /** @var PDO */
    private $pdo;

    /** @var bool */
    private $initialRun = true;

    public function createDatabase()
    {
        $this->connect();

        $this->dropDatabase(); // closes connection

        $this->connect();

        if (
            false === $this->pdo->exec(sprintf(
                "CREATE DATABASE %s",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE')
            ))
        ) {
            throw new Exception(sprintf(
                "I cannot create the PostgreSQL %s test database: %s",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE'),
                print_r($this->pdo->errorInfo(), true)
            ));
        }

        // PostgreSQL cannot switch database on same connection.
        $this->disconnect();

        $this->connect(true);

        if (false === $this->pdo->exec(file_get_contents($this->fixtureFile))) {
            throw new Exception(sprintf(
                "I cannot create the table for %s database. Check the %s file. %s ",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE'),
                $this->fixtureFile,
                print_r($this->pdo->errorInfo(), true)
            ));
        }

        $this->disconnect();
    }

    public function dropDatabase()
    {
        if (! $this->initialRun) {
            // Not possible to drop in PostgreSQL.
            // Connection is locking the database and trying to close it with unset()
            // does not trigger garbage collector on time to actually close it to free the lock.
            return;
        }
        $this->initialRun = false;

        $this->connect();

        $this->pdo->exec(sprintf(
            "DROP DATABASE IF EXISTS %s",
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE')
        ));

        $this->disconnect();
    }

    /**
     * @param bool $useDb add dbname using in dsn
     */
    protected function connect($useDb = false)
    {
        $dsn = 'pgsql:host=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME');

        if ($useDb) {
            $dsn .= ';dbname=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE');
        }

        $this->pdo = new PDO(
            $dsn,
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_USERNAME'),
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_PASSWORD')
        );
    }

    protected function disconnect()
    {
        $this->pdo = null;
    }
}
