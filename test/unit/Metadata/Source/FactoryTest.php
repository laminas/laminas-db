<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Metadata\Source;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Metadata\Source\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @dataProvider validAdapterProvider
     *
     * @param Adapter $adapter
     * @param string $expectedReturnClass
     */
    public function testCreateSourceFromAdapter(Adapter $adapter, $expectedReturnClass)
    {
        $source = Factory::createSourceFromAdapter($adapter);

        self::assertInstanceOf('Laminas\Db\Metadata\MetadataInterface', $source);
        self::assertInstanceOf($expectedReturnClass, $source);
    }

    public function validAdapterProvider()
    {
        $createAdapterForPlatform = function ($platformName) {
            $platform = $this->getMockBuilder('Laminas\Db\Adapter\Platform\PlatformInterface')->getMock();
            $platform
                ->expects($this->any())
                ->method('getName')
                ->willReturn($platformName)
            ;

            $adapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
                ->disableOriginalConstructor()
                ->getMock()
            ;

            $adapter
                ->expects($this->any())
                ->method('getPlatform')
                ->willReturn($platform)
            ;

            return $adapter;
        };

        return [
            // Description => [adapter, expected return class]
            'MySQL' => [$createAdapterForPlatform('MySQL'), 'Laminas\Db\Metadata\Source\MysqlMetadata'],
            'SQLServer' => [$createAdapterForPlatform('SQLServer'), 'Laminas\Db\Metadata\Source\SqlServerMetadata'],
            'SQLite' => [$createAdapterForPlatform('SQLite'), 'Laminas\Db\Metadata\Source\SqliteMetadata'],
            'PostgreSQL' => [$createAdapterForPlatform('PostgreSQL'), 'Laminas\Db\Metadata\Source\PostgresqlMetadata'],
            'Oracle' => [$createAdapterForPlatform('Oracle'), 'Laminas\Db\Metadata\Source\OracleMetadata'],
        ];
    }
}
