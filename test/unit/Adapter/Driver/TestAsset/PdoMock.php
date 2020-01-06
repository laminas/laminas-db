<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\TestAsset;

/**
 * Stub class
 */
class PdoMock extends \PDO
{
    public function __construct()
    {
    }

    public function beginTransaction()
    {
        return true;
    }

    public function commit()
    {
        return true;
    }

    public function getAttribute($attribute)
    {
    }

    public function rollBack()
    {
        return true;
    }
}
