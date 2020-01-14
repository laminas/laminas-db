<?php

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Postgresql;

use LaminasIntegrationTest\Db\Adapter\Driver\Pdo\AbstractAdapterTest;
use PHPUnit\DbUnit\TestCaseTrait;

class AdapterTest extends AbstractAdapterTest
{
    use AdapterTrait;

    const DB_SERVER_PORT = 5432;
}
