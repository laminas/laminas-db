<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Platform;

use function sqlsrv_connect;
use sqlsrv_query;
use sqlsrv_errors;

class SqlServerFixtureLoader implements FixtureLoader
{

    private $fixtureFilePrefix = __DIR__ . '/../TestFixtures/sqlsrv';
    /**
     * @var resource
     */
    private $connection;

    public function createDatabase()
    {
        $this->connect();

        if (false === sqlsrv_query($this->connection, sprintf(<<<'SQL'
                IF NOT EXISTS(SELECT * FROM sys.databases WHERE name = '%s')
                BEGIN
                    CREATE DATABASE [%s] 
                END
                SQL,
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE'),
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE')
        ))) {
            throw new \Exception(sprintf(
                "I cannot create the MSSQL %s database: %s",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE'),
                print_r(sqlsrv_errors(), true)
            ));
        }

        sqlsrv_query($this->connection, 'USE ' . getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE'));

        $fixtures = [
            'tables' => $this->fixtureFilePrefix.'.sql',
            'views' => $this->fixtureFilePrefix.'-views.sql',
            'triggers' => $this->fixtureFilePrefix.'-triggers.sql',
        ];

        foreach ($fixtures as $name => $fixtureFile) {
            if (false === sqlsrv_query($this->connection, file_get_contents($fixtureFile))) {
                throw new \Exception(sprintf(
                    "I cannot create the %s for %s database. Check the %s file. %s ",
                    $name,
                    getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE'),
                    $fixtureFile,
                    print_r(sqlsrv_errors(), true)
                ));
            }
        }

        $this->disconnect();
    }

    public function dropDatabase()
    {
        $this->connect();

        sqlsrv_query($this->connection, "USE master");
        sqlsrv_query($this->connection, sprintf(
            "ALTER DATABASE %s SET SINGLE_USER WITH ROLLBACK IMMEDIATE",
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE')
        ));

        if (false == sqlsrv_query($this->connection, sprintf(
            "DROP DATABASE %s",
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE')
        ))) {
            throw new \Exception(sprintf(
                "Unable to drop database %s. %s",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE'),
                print_r(sqlsrv_errors(), true)
            ));
        }

        $this->disconnect();
    }

    protected function connect()
    {
        $this->connection = sqlsrv_connect(
            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME'),
            [
                'UID' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME'),
                'PWD' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD'),
            ]
        );

        if (false === $this->connection) {
            throw new \Exception(sprintf(
                "Unable to connect %s. %s",
                getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE'),
                print_r(sqlsrv_errors(), true)
            ));
        }
    }

    protected function disconnect()
    {
        $this->connection = null;
    }
}
