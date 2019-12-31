<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Pdo\Pdo;

class PdoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pdo
     */
    protected $pdo;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->pdo = new Pdo(array());
    }

    /**
     * @covers Laminas\Db\Adapter\Driver\Pdo\Pdo::getDatabasePlatformName
     */
    public function testGetDatabasePlatformName()
    {
        // Test platform name for SqlServer
        $this->pdo->getConnection()->setConnectionParameters(array('pdodriver' => 'sqlsrv'));
        $this->assertEquals('SqlServer', $this->pdo->getDatabasePlatformName());
        $this->assertEquals('SQLServer', $this->pdo->getDatabasePlatformName(DriverInterface::NAME_FORMAT_NATURAL));
    }
}
