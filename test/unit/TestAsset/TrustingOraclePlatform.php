<?php

namespace LaminasTest\Db\TestAsset;

use Laminas\Db\Adapter\Platform\Oracle;

class TrustingOraclePlatform extends Oracle
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
