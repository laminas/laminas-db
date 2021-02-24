<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db\Adapter\Metadata;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Metadata\MetadataInterface;
use Laminas\Db\Metadata\Source\Factory;

/**
 * @group integration
 * @group integration-mysql
 */
class SqlServerTest extends AnsiTestCase
{
    /**
     * @var MetadataInterface
     */
    public $source = null;
    public $defaultSchema = null;

    protected function setUp():void
    {
        if (! getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV')) {
            $this->markTestSkipped(__CLASS__ . ' integration tests are not enabled!');
        }
        $driver = null;
        if (extension_loaded('sqlsrv')) {
            $driver = 'sqlsrv';
        } elseif (extension_loaded('pdo')) {
            $driver = 'pdo_sqlsrv';
        }

        if (! $driver) {
            $this->markTestSkipped(__CLASS__ . ' no valid SqlServer driver found!');
            return;
        }

        $this->source = Factory::createSourceFromAdapter(new Adapter([
            'driver' => $driver,
            'hostname' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME'),
            'username' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME'),
            'password' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD'),
            'database' => getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE')
        ]));

        $this->defaultSchema = null;
    }
}
