<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\TestAsset;

use Laminas\Db\Adapter\Driver\Pdo\Connection;

/**
 * Test asset class used only by {@see \LaminasTest\Db\Adapter\Driver\Pdo\ConnectionTransactionsTest}
 */
class ConnectionWrapper extends Connection
{
    public function __construct()
    {
        $this->resource = new PdoStubDriver('foo', 'bar', 'baz');
    }

    public function getNestedTransactionsCount()
    {
        return $this->nestedTransactionsCount;
    }
}
