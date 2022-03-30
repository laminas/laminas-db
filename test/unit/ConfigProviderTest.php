<?php

namespace LaminasTest\Db;

use Laminas\Db\Adapter;
use Laminas\Db\ConfigProvider;
use PHPUnit\Framework\TestCase;
use Zend\Db\Adapter\AdapterInterface;

class ConfigProviderTest extends TestCase
{
    /** @var array<string, array<array-key, string>> */
    private $config = [
        'abstract_factories' => [
            Adapter\AdapterAbstractServiceFactory::class,
        ],
        'factories'          => [
            Adapter\AdapterInterface::class => Adapter\AdapterServiceFactory::class,
        ],
        'aliases'            => [
            Adapter\Adapter::class          => Adapter\AdapterInterface::class,
            AdapterInterface::class         => Adapter\AdapterInterface::class,
            \Zend\Db\Adapter\Adapter::class => Adapter\Adapter::class,
        ],
    ];

    public function testProvidesExpectedConfiguration(): ConfigProvider
    {
        $provider = new ConfigProvider();
        self::assertEquals($this->config, $provider->getDependencyConfig());
        return $provider;
    }

    /**
     * @depends testProvidesExpectedConfiguration
     */
    public function testInvocationProvidesDependencyConfiguration(ConfigProvider $provider)
    {
        self::assertEquals(['dependencies' => $provider->getDependencyConfig()], $provider());
    }
}
