<?php
/**
 * @copyright 2017, Loft Digital, www.loftdigital.com
 * User: zdenek
 * Date: 08/03/17
 */

namespace ZendTest\Db\Adapter\Driver\Mysqli;

use Zend\Db\Adapter\Driver\Mysqli\Connection;

/**
 * @group integration
 */
class ConnectionIntegrationTest extends \PHPUnit_Framework_TestCase
{

    public function testConnectionOk()
    {
        $params = [
            'hostname' => getenv("TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME"),
            'username' => getenv("TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_USERNAME"),
            'password' => getenv("TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_PASSWORD"),
            'database' => getenv("TESTS_ZEND_DB_ADAPTER_DRIVER_MYSQL_DATABASE"),
        ];

        $connection = new Connection($params);
        $connection->connect();

        $this->assertTrue($connection->isConnected());
        $connection->disconnect();
    }

    /**
     * @expectedException Zend\Db\Adapter\Exception\RuntimeException
     * @expectedExceptionMessage Connection error
     */
    public function testConnectionFails()
    {
        $connection = new Connection([]);
        $connection->connect();
    }
}
