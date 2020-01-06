<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Mysql;

use Laminas\Db\Adapter\Adapter;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\Framework\TestCase;

class AdapterTest extends TestCase
{
    use AdapterTrait;

    /**
     * @covers \Laminas\Db\Adapter\Adapter::__construct()
     */
    public function testConnection()
    {
        $this->assertInstanceOf(Adapter::class, $this->adapter);
    }
}
