<?php

namespace LaminasTest\Db\Adapter\Driver\Pdo\TestAsset;

use Exception;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;

use function implode;
use function sprintf;

class SqliteMemoryPdo extends PDO
{
    /** @var PDOStatement&MockObject */
    protected $mockStatement;

    /** @param null|string $sql */
    public function __construct($sql = null)
    {
        parent::__construct('sqlite::memory:');

        if (empty($sql)) {
            return;
        }

        if (false === $this->exec($sql)) {
            throw new Exception(sprintf(
                "Error: %s, %s",
                $this->errorCode(),
                implode(",", $this->errorInfo())
            ));
        }
    }
}
