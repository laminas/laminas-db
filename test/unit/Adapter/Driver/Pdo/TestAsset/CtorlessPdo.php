<?php

namespace LaminasTest\Db\Adapter\Driver\Pdo\TestAsset;

use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;

class CtorlessPdo extends PDO
{
    /** @var PDOStatement&MockObject */
    protected $mockStatement;

    /** @param PDOStatement&MockObject $mockStatement */
    public function __construct($mockStatement)
    {
        $this->mockStatement = $mockStatement;
    }

    /**
     * @param string $sql
     * @param null|array $options
     * @return PDOStatement
     */
    public function prepare($sql, $options = null)
    {
        return $this->mockStatement;
    }
}
