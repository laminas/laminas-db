<?php

namespace LaminasIntegrationTest\Db\Platform;

use Exception;
use PDO;

use function file_get_contents;
use function getenv;
use function print_r;
use function sprintf;

class MysqlFixtureLoader implements FixtureLoader
{
    /** @var string */
    private $fixtureFile = __DIR__ . '/../TestFixtures/mysql.sql';

    /** @var PDO */
    private $pdo;

    public function createDatabase()
    {
        $this->connect();

        if (
            false === $this->pdo->exec(sprintf(
                "CREATE DATABASE IF NOT EXISTS %s",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE')
            ))
        ) {
            throw new Exception(sprintf(
                "I cannot create the MySQL %s test database: %s",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE'),
                print_r($this->pdo->errorInfo(), true)
            ));
        }

        $this->pdo->exec('USE ' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE'));

        if (false === $this->pdo->exec(file_get_contents($this->fixtureFile))) {
            throw new Exception(sprintf(
                "I cannot create the table for %s database. Check the %s file. %s ",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE'),
                $this->fixtureFile,
                print_r($this->pdo->errorInfo(), true)
            ));
        }

        $this->disconnect();
    }

    public function dropDatabase()
    {
        $this->connect();

        $this->pdo->exec(sprintf(
            "DROP DATABASE IF EXISTS %s",
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE')
        ));

        $this->disconnect();
    }

    protected function connect()
    {
        $dsn = 'mysql:host=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME');
        if (getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PORT')) {
            $dsn .= ';port=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PORT');
        }

        $this->pdo = new PDO(
            $dsn,
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_USERNAME'),
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PASSWORD')
        );
    }

    protected function disconnect()
    {
        $this->pdo = null;
    }
}
