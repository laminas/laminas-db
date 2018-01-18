<?php

namespace ZendTest\Db\IntegrationTest\Pdo\Mysql;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\Framework\TestCase;
use Zend\Db\Adapter\Adapter;

class ConnectionTest extends TestCase
{
    use ConnectionTrait;

    public function testConnection()
    {
        $this->assertInstanceOf(Adapter::class, $this->adapter);
    }
}
