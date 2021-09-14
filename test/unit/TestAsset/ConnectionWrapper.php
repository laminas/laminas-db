<?php

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
        parent::__construct();
    }

    public function getNestedTransactionsCount()
    {
        return $this->nestedTransactionsCount;
    }
}
