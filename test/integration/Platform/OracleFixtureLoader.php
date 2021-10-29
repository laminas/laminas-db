<?php

namespace LaminasIntegrationTest\Db\Platform;

use Exception;
use PDO;

use function file_get_contents;
use function getenv;
use function print_r;
use function sprintf;

class OracleFixtureLoader implements FixtureLoader
{
    const QUERY_DELIMITER = "\n;^";
    const FIXTURE_NAME_PATTERN = "#^\d+-.+\.sql$#";
    /** @var string */
    private $fixturesDir = __DIR__ . '/../TestFixtures/oracle/';

    /** @var PDO */
    private $pdo;

    public function createDatabase()
    {
        $this->connect();
        $fixturesPath = $this->getFixturesPath();
        foreach ($fixturesPath as $fixturePath) {
            $fixture = file_get_contents($fixturePath);
            $queries = explode(self::QUERY_DELIMITER, $fixture);
            foreach ($queries as $index => $query) {
                if (empty(trim($query))) {
                    continue;
                }
                $result = $this->pdo->exec($query);
                if (false === $result) {
                    $errorInfo = $this->pdo->errorInfo();
                    $maxFixtureLen = 2000;
                    throw new Exception(sprintf(
                        'Oracle fixtures error. %s',
                        print_r([
                            'fixturePath' => $fixturePath,
                            'errorInfo' => $errorInfo,
                            'query_index' => $index,
                            'query' => mb_strlen($query) > $maxFixtureLen ? mb_substr($query, 0, $maxFixtureLen) . '...CUT...' : $query,
                            'fixture' => mb_strlen($fixture) > $maxFixtureLen ? mb_substr($fixture, 0, $maxFixtureLen) . '...CUT...' : $fixture
                        ], true)
                    ));
                }
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
            if (!preg_match(self::FIXTURE_NAME_PATTERN, $fileName)) {
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
//            getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_DATABASE')
//        ));

        $this->disconnect();
    }

    /**
     * @see https://www.php.net/manual/en/ref.pdo-oci.connection.php
     * Connect to a database defined in tnsnames.ora
     * oci:dbname=mydb
     *  Connect using the Oracle Instant Client
     * oci:dbname=//localhost:1521/mydb
     */
    protected function connect()
    {
        $hostname = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_HOSTNAME');
        $database = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_DATABASE');
        $username = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_USERNAME');
        $password = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_OCI8_PASSWORD');

        $dsn = sprintf('oci:dbname=//%s/%s',
            $hostname,
            $database
        );
        $this->pdo = new PDO(
            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    protected function disconnect()
    {
        $this->pdo = null;
    }
}
