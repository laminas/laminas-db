<?php

namespace LaminasTest\Db\Adapter\Driver\Pdo\TestAsset;

use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use ReturnTypeWillChange;

class CtorlessPdo extends PDO
{
    /**
     * @var PDOStatement
     * @psalm-var PDOStatement&MockObject
     */
    protected $mockStatement;

    /**
     * @param PDOStatement $mockStatement
     * @psalm-param PDOStatement&MockObject $mockStatement
     */
    public function __construct($mockStatement)
    {
        $this->mockStatement = $mockStatement;
    }

    /**
     * @return PDOStatement|false
     */
    #[ReturnTypeWillChange]
    public function prepare($sql, $options = null)
    {
        return $this->mockStatement;
    }
}
