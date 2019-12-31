<?php

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\Pdo;
use Laminas\Db\Adapter\Platform\Sqlite;

/**
 * @group integration
 * @group integration-sqlite
 */
class SqliteIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public $adapters = array();

    public function testQuoteValueWithPdoSqlite()
    {
        if (!$this->adapters['pdo_sqlite'] instanceof \PDO) {
            $this->markTestSkipped('SQLite (PDO_SQLITE) not configured in unit test configuration file');
        }
        $sqlite = new Sqlite($this->adapters['pdo_sqlite']);
        $value = $sqlite->quoteValue('value');
        $this->assertEquals('\'value\'', $value);

        $sqlite = new Sqlite(new Pdo\Pdo(new Pdo\Connection($this->adapters['pdo_sqlite'])));
        $value = $sqlite->quoteValue('value');
        $this->assertEquals('\'value\'', $value);
    }
}
