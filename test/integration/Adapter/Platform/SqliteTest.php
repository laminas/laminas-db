<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\Pdo;
use Laminas\Db\Adapter\Platform\Sqlite;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 * @group integration-sqlite
 */
class SqliteTest extends TestCase
{
    public $adapters = [];

    protected function setUp()
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLITE_MEMORY')) {
            $this->markTestSkipped(__CLASS__ . ' integration tests are not enabled!');
        }
        if (extension_loaded('pdo')) {
            $this->adapters['pdo_sqlite'] = new \PDO(
                'sqlite::memory:'
            );
        }
    }

    public function testQuoteValueWithPdoSqlite()
    {
        if (! $this->adapters['pdo_sqlite'] instanceof \PDO) {
            $this->markTestSkipped('SQLite (PDO_SQLITE) not configured in unit test configuration file');
        }
        $sqlite = new Sqlite($this->adapters['pdo_sqlite']);
        $value = $sqlite->quoteValue('value');
        self::assertEquals('\'value\'', $value);

        $sqlite = new Sqlite(new Pdo\Pdo(new Pdo\Connection($this->adapters['pdo_sqlite'])));
        $value = $sqlite->quoteValue('value');
        self::assertEquals('\'value\'', $value);
    }
}
