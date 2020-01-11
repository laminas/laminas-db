<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Platform;

class PgsqlFixtureLoader implements FixtureLoader
{

    private $fixtureFile = __DIR__ . '/../TestFixtures/pgsql.sql';
    /**
     * @var \PDO
     */
    private $pdo;
    private $initialRun = true;

    public function createDatabase()
    {
        $this->pdo = new \PDO(
            'pgsql:host=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME'),
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_USERNAME'),
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_PASSWORD')
        );

        $this->dropDatabase();
        if (false === $this->pdo->exec(sprintf(
            "CREATE DATABASE %s",
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE')
        ))) {
            throw new \Exception(sprintf(
                "I cannot create the PostgreSQL %s test database: %s",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE'),
                print_r($this->pdo->errorInfo(), true)
            ));
        }

        // PostgreSQL cannot switch database on same connection.
        unset($this->pdo);
        $this->pdo = new \PDO(
            'pgsql:host=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME') . ';' .
            'dbname=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE'),
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_USERNAME'),
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_PASSWORD')
        );

        if (false === $this->pdo->exec(file_get_contents($this->fixtureFile))) {
            throw new \Exception(sprintf(
                "I cannot create the table for %s database. Check the %s file. %s ",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE'),
                $this->fixtureFile,
                print_r($this->pdo->errorInfo(), true)
            ));
        }
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

        $this->pdo->exec(sprintf(
            "DROP DATABASE IF EXISTS %s",
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE')
        ));
    }
}
