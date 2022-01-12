<?php

namespace LaminasTest\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterAbstractServiceFactory;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class AdapterAbstractServiceFactoryTest extends TestCase
{
    /** @var ServiceLocatorInterface */
    private $serviceManager;

    protected function setUp(): void
    {
        $this->serviceManager = new ServiceManager();

        $config = new Config([
            'abstract_factories' => [AdapterAbstractServiceFactory::class],
        ]);
        $config->configureServiceManager($this->serviceManager);

        $this->serviceManager->setService('config', [
            'db' => [
                'adapters' => [
                    'Laminas\Db\Adapter\Writer' => [
                        'driver' => 'mysqli',
                    ],
                    'Laminas\Db\Adapter\Reader' => [
                        'driver' => 'mysqli',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @return array
     */
    public function providerValidService()
    {
        return [
            ['Laminas\Db\Adapter\Writer'],
            ['Laminas\Db\Adapter\Reader'],
        ];
    }

    /**
     * @return array
     */
    public function providerInvalidService()
    {
        return [
            ['Laminas\Db\Adapter\Unknown'],
        ];
    }

    /**
     * @param string $service
     * @dataProvider providerValidService
     * @requires extension mysqli
     */
    public function testValidService($service)
    {
        $actual = $this->serviceManager->get($service);
        self::assertInstanceOf(Adapter::class, $actual);
    }

    /**
     * @dataProvider providerInvalidService
     * @param string $service
     */
    public function testInvalidService($service)
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->serviceManager->get($service);
    }
}
