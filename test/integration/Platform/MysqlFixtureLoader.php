<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Platform;

class MysqlFixtureLoader implements FixtureLoader
{

    private $fixtureFile = __DIR__ . '/../TestFixtures/mysql.sql';
    /**
     * @var \PDO
     */
    private $pdo;

    public function createDatabase()
    {
        $this->connect();

        if (false === $this->pdo->exec(sprintf(
            "CREATE DATABASE IF NOT EXISTS %s",
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE')
        ))) {
            throw new \Exception(sprintf(
                "I cannot create the MySQL %s test database: %s",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE'),
                print_r($this->pdo->errorInfo(), true)
            ));
        }

        $this->pdo->exec('USE ' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE'));

        if (false === $this->pdo->exec(file_get_contents($this->fixtureFile))) {
            throw new \Exception(sprintf(
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
        $this->pdo = new \PDO(
            'mysql:host=' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME'),
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_USERNAME'),
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PASSWORD')
        );
    }

    protected function disconnect()
    {
        $this->pdo = null;
    }
}
