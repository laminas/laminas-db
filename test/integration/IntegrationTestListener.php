<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendIntegrationTest\Db;

use Exception;
use PDO;
use PDOException;
use PHPUnit\Framework\BaseTestListener;
use PHPUnit\Framework\TestSuite;

class IntegrationTestListener extends BaseTestListener
{
    /**
     * @var PDO
     */
    private $pdo;

    public function startTestSuite(TestSuite $suite)
    {
        if ($suite->getName() !== 'integration test') {
            return;
        }
        printf("\nIntegration test started.\n");

        if (getenv('TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL')) {
            $this->createMysqlDatabase(__DIR__ . '/TestAsset/mysql.sql');
        }
    }

    public function endTestSuite(TestSuite $suite)
    {
        if ($suite->getName() !== 'integration test') {
            return;
        }
        printf("\nIntegration test ended.\n");

        if (getenv('TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL')) {
            $this->dropMysqlDatabase();
        }
    }

    private function createMysqlDatabase($dbFile)
    {
        $this->pdo = new PDO(
            'mysql:host=' . getenv('TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME'),
            getenv('TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_USERNAME'),
            getenv('TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_PASSWORD')
        );
        if (false === $this->pdo->exec(sprintf(
            "CREATE DATABASE IF NOT EXISTS %s",
            getenv('TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_DATABASE')
        ))) {
            throw new Exception(sprintf(
                "I cannot create the MySQL %s test database",
                getenv('TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_DATABASE')
            ));
        }

        $this->pdo->exec('USE ' . getenv('TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_DATABASE'));

        if (false === $this->pdo->exec(file_get_contents($dbFile))) {
            throw new Exception(sprintf(
                "I cannot create the table for %s database. Check the %s file. ",
                getenv('TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_DATABASE'),
                $dbFile
            ));
        }
    }

    private function dropMysqlDatabase()
    {
        if (!$this->pdo instanceOf PDO) {
            return;
        }
        $this->pdo->exec(sprintf(
            "DROP DATABASE IF EXISTS %s",
            getenv('TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_DATABASE')
        ));
    }
}
