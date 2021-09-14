<?php

namespace LaminasTest\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterServiceFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function extension_loaded;

class AdapterServiceFactoryTest extends TestCase
{
    /** @var ServiceLocatorInterface&MockObject */
    private $services;

    /** @var AdapterServiceFactory */
    private $factory;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Adapter factory tests require pdo_sqlite');
        }

        $this->services = $this->createMock(ServiceLocatorInterface::class);

        $this->factory = new AdapterServiceFactory();
    }

    public function testV2FactoryReturnsAdapter()
    {
        $this->services
            ->method('get')
            ->with('config')
            ->willReturn([
                'db' => [
                    'driver'   => 'Pdo_Sqlite',
                    'database' => 'sqlite::memory:',
                ],
            ]);

        $adapter = $this->factory->createService($this->services);
        self::assertInstanceOf(Adapter::class, $adapter);
    }

    public function testV3FactoryReturnsAdapter()
    {
        $this->services
            ->method('get')
            ->with('config')
            ->willReturn([
                'db' => [
                    'driver'   => 'Pdo_Sqlite',
                    'database' => 'sqlite::memory:',
                ],
            ]);

        $adapter = $this->factory->__invoke($this->services, Adapter::class);
        self::assertInstanceOf(Adapter::class, $adapter);
    }
}
