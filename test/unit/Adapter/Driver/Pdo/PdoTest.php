<?php

namespace LaminasTest\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Pdo\Pdo;
use Laminas\Db\Adapter\Driver\Pdo\Result;
use Laminas\Db\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class PdoTest extends TestCase
{
    /** @var Pdo */
    protected $pdo;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
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

    /** @psalm-return array<array-key, array{0: int|string, 1: null|string, 2: string}> */
    public function getParamsAndType(): array
    {
        return [
            ['foo', null, ':foo'],
            ['foo_bar', null, ':foo_bar'],
            ['123foo', null, ':123foo'],
            [1, null, '?'],
            ['1', null, '?'],
            ['foo', Pdo::PARAMETERIZATION_NAMED, ':foo'],
            ['foo_bar', Pdo::PARAMETERIZATION_NAMED, ':foo_bar'],
            ['123foo', Pdo::PARAMETERIZATION_NAMED, ':123foo'],
            [1, Pdo::PARAMETERIZATION_NAMED, ':1'],
            ['1', Pdo::PARAMETERIZATION_NAMED, ':1'],
            [':foo', null, ':foo'],
        ];
    }

    /**
     * @dataProvider getParamsAndType
     * @param int|string $name
     */
    public function testFormatParameterName($name, ?string $type, string $expected)
    {
        $result = $this->pdo->formatParameterName($name, $type);
        $this->assertEquals($expected, $result);
    }

    /** @psalm-return array<array-key, array{0: string}> */
    public function getInvalidParamName(): array
    {
        return [
            ['foo%'],
            ['foo-'],
            ['foo$'],
            ['foo0!'],
        ];
    }

    /**
     * @dataProvider getInvalidParamName
     */
    public function testFormatParameterNameWithInvalidCharacters(string $name)
    {
        $this->expectException(RuntimeException::class);
        $this->pdo->formatParameterName($name);
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Pdo::getResultPrototype
     */
    public function testGetResultPrototype()
    {
        $resultPrototype = $this->pdo->getResultPrototype();

        self::assertInstanceOf(Result::class, $resultPrototype);
    }
}
