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
            [ 'foo', null, ':foo' ],
            [ 'foo_bar', null, ':foo_bar' ],
            [ '123foo', null, ':123foo' ],
            [ 1, null, '?' ],
            [ '1', null, '?' ],
            [ 'foo', Pdo::PARAMETERIZATION_NAMED, ':foo' ],
            [ 'foo_bar', Pdo::PARAMETERIZATION_NAMED, ':foo_bar' ],
            [ '123foo', Pdo::PARAMETERIZATION_NAMED, ':123foo' ],
            [ 1, Pdo::PARAMETERIZATION_NAMED, ':1' ],
            [ '1', Pdo::PARAMETERIZATION_NAMED, ':1' ],
            [ ':foo', null, ':foo' ],
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

    public function getInvalidParamName()
    {
        return [
            [ 'foo%' ],
            [ 'foo-' ],
            [ 'foo$' ],
            [ 'foo0!' ]
        ];
    }

    /**
     * @dataProvider getInvalidParamName
     * @expectedException Laminas\Db\Exception\RuntimeException
     */
    public function testFormatParameterNameWithInvalidCharacters($name)
    {
        $this->pdo->formatParameterName($name);
    }
}
