<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\Pdo;
use Laminas\Db\Adapter\Driver\Pgsql;
use Laminas\Db\Adapter\Platform\Postgresql;

/**
 * @group integration
 * @group integration-postgres
 */
class PostgresqlIntegrationTest extends \PHPUnit_Framework_TestCase
{

    public $adapters = array();

    public function testQuoteValueWithPgsql()
    {
        if (!is_resource($this->adapters['pgsql'])) {
            $this->markTestSkipped('Postgres (pgsql) not configured in unit test configuration file');
        }
        $pgsql = new Postgresql($this->adapters['pgsql']);
        $value = $pgsql->quoteValue('value');
        $this->assertEquals('\'value\'', $value);

        $pgsql = new Postgresql(new Pgsql\Pgsql(new Pgsql\Connection($this->adapters['pgsql'])));
        $value = $pgsql->quoteValue('value');
        $this->assertEquals('\'value\'', $value);

    }

    public function testQuoteValueWithPdoPgsql()
    {
        if (!$this->adapters['pdo_pgsql'] instanceof \PDO) {
            $this->markTestSkipped('Postgres (PDO_PGSQL) not configured in unit test configuration file');
        }
        $pgsql = new Postgresql($this->adapters['pdo_pgsql']);
        $value = $pgsql->quoteValue('value');
        $this->assertEquals('\'value\'', $value);

        $pgsql = new Postgresql(new Pdo\Pdo(new Pdo\Connection($this->adapters['pdo_pgsql'])));
        $value = $pgsql->quoteValue('value');
        $this->assertEquals('\'value\'', $value);
    }

}
