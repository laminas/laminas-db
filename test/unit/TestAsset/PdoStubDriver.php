<?php
namespace LaminasTest\Db\TestAsset;

class PdoStubDriver extends \PDO
{
    public function beginTransaction(): bool
    {
        return true;
    }

    public function commit(): bool
    {
        return true;
    }

    public function __construct($dsn, $user, $password)
    {
    }

    public function rollBack(): bool
    {
        return true;
    }
}
