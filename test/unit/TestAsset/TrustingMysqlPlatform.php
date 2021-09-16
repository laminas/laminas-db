<?php

namespace LaminasTest\Db\TestAsset;

use Laminas\Db\Adapter\Platform\Mysql;

class TrustingMysqlPlatform extends Mysql
{
    /**
     * @param string $value
     * @return string
     */
    public function quoteValue($value)
    {
        return $this->quoteTrustedValue($value);
    }
}
