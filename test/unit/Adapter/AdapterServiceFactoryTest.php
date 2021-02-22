<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter;

use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterServiceFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AdapterServiceFactoryTest extends TestCase
{
    use ProphecyTrait;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Adapter factory tests require pdo_sqlite');
        }

        $this->services = $this->prophesize(ServiceLocatorInterface::class);
        $this->services->willImplement(ContainerInterface::class);

        $this->factory = new AdapterServiceFactory();
    }

    public function testV2FactoryReturnsAdapter()
    {
        $this->services->get('config')->willReturn([
            'db' => [
                'driver' => 'Pdo_Sqlite',
                'database' => 'sqlite::memory:',
            ],
        ]);

        $adapter = $this->factory->createService($this->services->reveal());
        self::assertInstanceOf(Adapter::class, $adapter);
    }

    public function testV3FactoryReturnsAdapter()
    {
        $this->services->get('config')->willReturn([
            'db' => [
                'driver' => 'Pdo_Sqlite',
                'database' => 'sqlite::memory:',
            ],
        ]);

        $adapter = $this->factory->__invoke($this->services->reveal(), Adapter::class);
        self::assertInstanceOf(Adapter::class, $adapter);
    }
}
