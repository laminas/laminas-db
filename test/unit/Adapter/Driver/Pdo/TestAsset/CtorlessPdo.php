<?php

namespace LaminasTest\Db\Adapter\Driver\Pdo\TestAsset;

use PDOStatement;

class CtorlessPdo extends \Pdo
{
    /**
     * @var PDOStatement
     */
    protected $mockStatement;

    public function __construct(PDOStatement $mockStatement)
    {
        $this->mockStatement = $mockStatement;
    }

    public function prepare($sql, $options = null): PDOStatement
    {
        return $this->mockStatement;
    }
}
