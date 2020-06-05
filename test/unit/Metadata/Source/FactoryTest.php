<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Metadata\Source;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Metadata\MetadataInterface;
use Laminas\Db\Metadata\Source\Factory;
use Laminas\Db\Metadata\Source\MysqlMetadata;
use Laminas\Db\Metadata\Source\OracleMetadata;
use Laminas\Db\Metadata\Source\PostgresqlMetadata;
use Laminas\Db\Metadata\Source\SqliteMetadata;
use Laminas\Db\Metadata\Source\SqlServerMetadata;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @dataProvider validAdapterProvider
     * @param string $expectedReturnClass
     */
    public function testCreateSourceFromAdapter(Adapter $adapter, $expectedReturnClass)
    {
        $source = Factory::createSourceFromAdapter($adapter);

        self::assertInstanceOf(MetadataInterface::class, $source);
        self::assertInstanceOf($expectedReturnClass, $source);
    }

    public function validAdapterProvider()
    {
        $createAdapterForPlatform = function ($platformName) {
            $platform = $this->getMockBuilder(PlatformInterface::class)->getMock();
            $platform
                ->expects($this->any())
                ->method('getName')
                ->willReturn($platformName);

            $adapter = $this->getMockBuilder(Adapter::class)
                ->disableOriginalConstructor()
                ->getMock();

            $adapter
                ->expects($this->any())
                ->method('getPlatform')
                ->willReturn($platform);

            return $adapter;
        };

        return [
            // Description => [adapter, expected return class]
            'MySQL'      => [$createAdapterForPlatform('MySQL'), MysqlMetadata::class],
            'SQLServer'  => [$createAdapterForPlatform('SQLServer'), SqlServerMetadata::class],
            'SQLite'     => [$createAdapterForPlatform('SQLite'), SqliteMetadata::class],
            'PostgreSQL' => [$createAdapterForPlatform('PostgreSQL'), PostgresqlMetadata::class],
            'Oracle'     => [$createAdapterForPlatform('Oracle'), OracleMetadata::class],
        ];
    }
}
