<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Mysql;

use LaminasIntegrationTest\Db\Adapter\Driver\Pdo\AbstractAdapterTest;

class AdapterTest extends AbstractAdapterTest
{
    use AdapterTrait;

    public const DB_SERVER_PORT = 3306;
}
