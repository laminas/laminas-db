<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter;

use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class AdapterAbstractServiceFactoryTest extends TestCase
{
    /**
     * @var \Laminas\ServiceManager\ServiceLocatorInterface
     */
    private $serviceManager;

    protected function setUp()
    {
        $this->serviceManager = new ServiceManager();

        $config = new Config([
            'abstract_factories' => ['Laminas\Db\Adapter\AdapterAbstractServiceFactory'],
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
        self::assertInstanceOf('Laminas\Db\Adapter\Adapter', $actual);
    }

    /**
     * @dataProvider providerInvalidService
     *
     * @param string $service
     */
    public function testInvalidService($service)
    {
        $this->expectException('\Laminas\ServiceManager\Exception\ServiceNotFoundException');
        $this->serviceManager->get($service);
    }
}
