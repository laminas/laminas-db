<?php

namespace ZendIntegrationTest\Db\Pdo\Mysql;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\Framework\TestCase;
use Zend\Db\Adapter\Adapter;

class AdapterTest extends TestCase
{
    use AdapterTrait;

    public function testConnection()
    {
        $this->assertInstanceOf(Adapter::class, $this->adapter);
    }
}
