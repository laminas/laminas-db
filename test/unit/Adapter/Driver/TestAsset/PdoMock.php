<?php

namespace LaminasTest\Db\Adapter\Driver\TestAsset;

use PDO;

/**
 * Stub class
 */
class PdoMock extends PDO
{
    public function __construct()
    {
    }

    /** @return bool */
    public function beginTransaction()
    {
        return true;
    }

    /** @return bool */
    public function commit()
    {
        return true;
    }

    /**
     * @param string $attribute
     * @return null
     */
    public function getAttribute($attribute)
    {
        return null;
    }

    /** @return bool */
    public function rollBack()
    {
        return true;
    }
}
