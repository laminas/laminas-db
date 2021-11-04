<?php

namespace LaminasIntegrationTest\Db\Platform;

use Exception;
use PDO;

use function file_get_contents;
use function getenv;
use function print_r;
use function sprintf;

class SqliteFixtureLoader implements FixtureLoader
{
    /** @var string */
    private $fixturesDir = __DIR__ . '/../TestFixtures/sqlite/';

    /** @var PDO */
    private $pdo;

    public function createDatabase()
    {
        $this->connect();
        $fixturesPath = $this->getFixturesPath();
        foreach ($fixturesPath as $fixturePath) {
            $fixture = file_get_contents($fixturePath);
            $result = $this->pdo->exec($fixture);
            if (false === $result) {
                $errorInfo = $this->pdo->errorInfo();
                throw new Exception(sprintf(
                    "Sqlite fixtures error. Check the %s file. %s ",
                    $this->fixtureFile,
                    print_r($errorInfo, true)
                ));
            }
        }

        $this->disconnect();
    }

    protected function getFixturesPath()
    {
        $dir = realpath($this->fixturesDir);
        if (!is_dir($dir)) {
            throw new Exception('Dir not found');
        }
        $files = scandir($dir);
        $result = [];
        foreach ($files as $fileName) {
            if (!preg_match("#^\d+-.+\.sql$#", $fileName)) {
                continue;
            }
            $filePath = realpath($dir . DIRECTORY_SEPARATOR . $fileName);
            if (!is_file($filePath)) {
                continue;
            }
            $result[] = $filePath;
        }
        return $result;

    }

    public function dropDatabase()
    {
        $this->connect();

//        $this->pdo->exec(sprintf(
//            "DROP DATABASE IF EXISTS %s",
//            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE')
//        ));

        $this->disconnect();
    }

    protected function connect()
    {
        $database = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLITE_FILE_DATABASE');
        $dsn = 'sqlite:' . $database;
        $this->pdo = new PDO($dsn);
    }

    protected function disconnect()
    {
        $this->pdo = null;
    }
}
