<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Pdo\Pdo;
use PHPUnit\Framework\TestCase;

class PdoTest extends TestCase
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
        $this->pdo = new Pdo([]);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Pdo::getDatabasePlatformName
     */
    public function testGetDatabasePlatformName()
    {
        // Test platform name for SqlServer
        $this->pdo->getConnection()->setConnectionParameters(['pdodriver' => 'sqlsrv']);
        self::assertEquals('SqlServer', $this->pdo->getDatabasePlatformName());
        self::assertEquals('SQLServer', $this->pdo->getDatabasePlatformName(DriverInterface::NAME_FORMAT_NATURAL));
    }

    public function getParamsAndType()
    {
        return [
            [ 'foo', null, ':' . md5('foo')],
            [ 'foo-', null, ':' . md5('foo-')],
            [ 'foo$', null, ':' . md5('foo$')],
            [ 1, null, '?' ],
            [ '1', null, '?'],
            [ 'foo', Pdo::PARAMETERIZATION_NAMED, ':' . md5('foo')],
            [ 'foo-', Pdo::PARAMETERIZATION_NAMED, ':' . md5('foo-')],
            [ 'foo$', Pdo::PARAMETERIZATION_NAMED, ':' . md5('foo$')],
            [ 1, Pdo::PARAMETERIZATION_NAMED, ':' . md5('1')],
            [ '1', Pdo::PARAMETERIZATION_NAMED, ':' . md5('1')],
        ];
    }

    /**
     * @dataProvider getParamsAndType
     */
    public function testFormatParameterName($name, $type, $expected)
    {
        $result = $this->pdo->formatParameterName($name, $type);
        $this->assertEquals($expected, $result);
    }
}
