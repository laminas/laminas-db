<?php

namespace LaminasTest\Db\TestAsset;

use PDO;

class PdoStubDriver extends PDO
{
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
     * @param string $dsn
     * @param string $user
     * @param string $password
     */
    public function __construct($dsn, $user, $password)
    {
    }

    /** @return bool */
    public function rollBack()
    {
        return true;
    }
}
