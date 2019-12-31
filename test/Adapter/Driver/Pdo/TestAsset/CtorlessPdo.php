<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pdo\TestAsset;

class CtorlessPdo extends \Pdo
{
    protected $mockStatement;

    public function __construct($mockStatement)
    {
        $this->mockStatement = $mockStatement;
    }

    public function prepare($sql, $options = null)
    {
        return $this->mockStatement;
    }
}
