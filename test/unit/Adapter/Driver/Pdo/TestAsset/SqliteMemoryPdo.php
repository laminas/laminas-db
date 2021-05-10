<?php

namespace LaminasTest\Db\Adapter\Driver\Pdo\TestAsset;

class SqliteMemoryPdo extends \Pdo
{
    protected $mockStatement;

    public function __construct($sql = null)
    {
        parent::__construct('sqlite::memory:');

        if (empty($sql)) {
            return;
        }
        if (false === $this->exec($sql)) {
            throw new \Exception(sprintf(
                "Error: %s, %s",
                $this->errorCode(),
                implode(",", $this->errorInfo())
            ));
        }
    }
}
