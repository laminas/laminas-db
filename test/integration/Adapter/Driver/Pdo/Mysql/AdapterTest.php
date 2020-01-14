<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Adapter\Driver\Pdo\Mysql;

use LaminasIntegrationTest\Db\Adapter\Driver\Pdo\AbstractAdapterTest;

class AdapterTest extends AbstractAdapterTest
{
    use AdapterTrait;

    const DB_SERVER_PORT = 3306;
}
