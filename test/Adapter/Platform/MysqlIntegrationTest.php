<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\Mysqli;
use Laminas\Db\Adapter\Driver\Pdo;
use Laminas\Db\Adapter\Platform\Mysql;

/**
 * @group integration
 * @group integration-mysql
 */
class MysqlIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public $adapters = array();

    public function testQuoteValueWithMysqli()
    {
        if (!$this->adapters['mysqli'] instanceof \Mysqli) {
            $this->markTestSkipped('MySQL (Mysqli) not configured in unit test configuration file');
        }
        $mysql = new Mysql($this->adapters['mysqli']);
        $value = $mysql->quoteValue('value');
        $this->assertEquals('\'value\'', $value);

        $mysql = new Mysql(new Mysqli\Mysqli(new Mysqli\Connection($this->adapters['mysqli'])));
        $value = $mysql->quoteValue('value');
        $this->assertEquals('\'value\'', $value);

    }

    public function testQuoteValueWithPdoMysql()
    {
        if (!$this->adapters['pdo_mysql'] instanceof \PDO) {
            $this->markTestSkipped('MySQL (PDO_Mysql) not configured in unit test configuration file');
        }
        $mysql = new Mysql($this->adapters['pdo_mysql']);
        $value = $mysql->quoteValue('value');
        $this->assertEquals('\'value\'', $value);

        $mysql = new Mysql(new Pdo\Pdo(new Pdo\Connection($this->adapters['pdo_mysql'])));
        $value = $mysql->quoteValue('value');
        $this->assertEquals('\'value\'', $value);
    }
}
