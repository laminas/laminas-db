<?php

namespace LaminasTest\Db\Adapter\Driver\TestAsset;

/**
 * Stub class
 */
class PdoMock extends \PDO
{
    public function __construct()
    {
    }

    public function beginTransaction()
    {
        return true;
    }

    public function commit()
    {
        return true;
    }

    public function getAttribute($attribute)
    {
    }

    public function rollBack()
    {
        return true;
    }
}
