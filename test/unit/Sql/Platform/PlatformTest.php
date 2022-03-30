<?php

namespace LaminasTest\Db\Sql\Platform;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\StatementContainer;
use Laminas\Db\Sql\Exception\RuntimeException;
use Laminas\Db\Sql\Platform\Platform;
use LaminasTest\Db\TestAsset;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

class PlatformTest extends TestCase
{
    public function testResolveDefaultPlatform()
    {
        $adapter  = $this->resolveAdapter('sql92');
        $platform = new Platform($adapter);

        $reflectionMethod = new ReflectionMethod($platform, 'resolvePlatform');

        $reflectionMethod->setAccessible(true);

        self::assertEquals($adapter->getPlatform(), $reflectionMethod->invoke($platform, null));
    }

    public function testResolvePlatformName()
    {
        $platform = new Platform($this->resolveAdapter('sql92'));

        $reflectionMethod = new ReflectionMethod($platform, 'resolvePlatformName');

        $reflectionMethod->setAccessible(true);

        self::assertEquals('mysql', $reflectionMethod->invoke($platform, new TestAsset\TrustingMysqlPlatform()));
        self::assertEquals('sqlserver', $reflectionMethod->invoke(
            $platform,
            new TestAsset\TrustingSqlServerPlatform()
        ));
        self::assertEquals('oracle', $reflectionMethod->invoke($platform, new TestAsset\TrustingOraclePlatform()));
        self::assertEquals('sql92', $reflectionMethod->invoke($platform, new TestAsset\TrustingSql92Platform()));
    }

    /**
     * @group 6890
     */
    public function testAbstractPlatformCrashesGracefullyOnMissingDefaultPlatform()
    {
        $adapter            = $this->resolveAdapter('sql92');
        $reflectionProperty = new ReflectionProperty($adapter, 'platform');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($adapter, null);

        $platform         = new Platform($adapter);
        $reflectionMethod = new ReflectionMethod($platform, 'resolvePlatform');

        $reflectionMethod->setAccessible(true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('$this->defaultPlatform was not set');

        $reflectionMethod->invoke($platform, null);
    }

    /**
     * @group 6890
     */
    public function testAbstractPlatformCrashesGracefullyOnMissingDefaultPlatformWithGetDecorators()
    {
        $adapter            = $this->resolveAdapter('sql92');
        $reflectionProperty = new ReflectionProperty($adapter, 'platform');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($adapter, null);

        $platform         = new Platform($adapter);
        $reflectionMethod = new ReflectionMethod($platform, 'resolvePlatform');

        $reflectionMethod->setAccessible(true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('$this->defaultPlatform was not set');

        $platform->getDecorators();
    }

    /**
     * @param string $platformName
     * @return Adapter
     */
    protected function resolveAdapter($platformName)
    {
        $platform = null;

        switch ($platformName) {
            case 'sql92':
                $platform = new TestAsset\TrustingSql92Platform();
                break;
            case 'MySql':
                $platform = new TestAsset\TrustingMysqlPlatform();
                break;
            case 'Oracle':
                $platform = new TestAsset\TrustingOraclePlatform();
                break;
            case 'SqlServer':
                $platform = new TestAsset\TrustingSqlServerPlatform();
                break;
        }

        /** @var DriverInterface|MockObject $mockDriver */
        $mockDriver = $this->getMockBuilder(DriverInterface::class)->getMock();

        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnCallback(function () {
            return new StatementContainer();
        }));

        return new Adapter($mockDriver, $platform);
    }
}
