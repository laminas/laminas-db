<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db;

use Laminas\Db\Adapter;
use Laminas\Db\ConfigProvider;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    private $config = [
        'abstract_factories' => [
            Adapter\AdapterAbstractServiceFactory::class,
        ],
        'factories' => [
            Adapter\AdapterInterface::class => Adapter\AdapterServiceFactory::class,
        ],
        'aliases' => [
            Adapter\Adapter::class => Adapter\AdapterInterface::class,
            \Zend\Db\Adapter\AdapterInterface::class => Adapter\AdapterInterface::class,
            \Zend\Db\Adapter\Adapter::class => Adapter\Adapter::class,
        ],
    ];

    public function testProvidesExpectedConfiguration()
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
