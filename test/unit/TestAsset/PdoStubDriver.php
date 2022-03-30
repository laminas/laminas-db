<?php

namespace LaminasTest\Db\TestAsset;

use PDO;

class PdoStubDriver extends PDO
{
    public function beginTransaction(): bool
    {
        return true;
    }

    public function commit(): bool
    {
        return true;
    }

    /**
     * @param string $dsn
     * @param string $user
     * @param string $password
     */
    public function __construct($dsn, $user, $password)
    {
    }

    public function rollBack(): bool
    {
        return true;
    }
}
