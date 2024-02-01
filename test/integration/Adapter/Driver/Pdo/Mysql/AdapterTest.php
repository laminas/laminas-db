<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Mysql;

use Laminas\Db\Adapter\Adapter;
use LaminasIntegrationTest\Db\Adapter\Driver\Pdo\AbstractAdapterTest;

class AdapterTest extends AbstractAdapterTest
{
    use AdapterTrait;

    /** @var Adapter */
    protected $adapter;
    public const DB_SERVER_PORT = 3306;
}
