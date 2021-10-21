<?php

namespace LaminasIntegrationTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Sql;
use Laminas\Db\TableGateway\TableGateway;

use function extension_loaded;
use function getenv;

/**
 * @group integration
 * @group integration-sqlite
 */
class SqliteFileTest extends SqliteTest
{
    /** @var array<string, resource|\PDO> */
    public $adapters = [];

    /**
     * @return Adapter|mixed
     */
    protected function getLaminasAdapter()
    {
        $adapter = $this->adapters['laminas'];
        return $adapter;
    }

    protected function setUp(): void
    {
        if (!getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLITE_FILE')) {
            $this->markTestSkipped(self::class . ' integration tests are not enabled!');
        }
        $database = getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLITE_FILE_DATABASE');
        $dsn = 'sqlite:' . $database;
        /**
         * @see \LaminasIntegrationTest\Db\Adapter\Platform\SqliteTest::setUp
         */
        if (extension_loaded('pdo')) {
            $this->adapters['pdo_sqlite'] = new \PDO($dsn);
        }
    }

}
