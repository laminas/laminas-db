<?php

namespace LaminasTest\Db\Adapter\Driver\TestAsset;

use PDO;
use ReturnTypeWillChange;

/**
 * Stub class
 */
class PdoMock extends PDO
{
    public function __construct()
    {
    }

    public function beginTransaction(): bool
    {
        return true;
    }

    public function commit(): bool
    {
        return true;
    }

    /**
     * @param string $attribute
     * @return null
     */
    #[ReturnTypeWillChange]
    public function getAttribute($attribute)
    {
        return null;
    }

    public function rollBack(): bool
    {
        return true;
    }
}
