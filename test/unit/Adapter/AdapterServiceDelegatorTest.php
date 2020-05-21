<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterAwareInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\AdapterServiceDelegator;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ServiceManager;
use LaminasTest\Db\Adapter\TestAsset\ConcreteAdapterAwareObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use stdClass;

class AdapterServiceDelegatorTest extends TestCase
{
    public function testSetAdapterShouldBeCalledForExistingAdapter() : void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(AdapterInterface::class)
            ->shouldBeCalledOnce()
            ->willReturn(true);
        $container->get(AdapterInterface::class)
            ->shouldBeCalledOnce()
            ->willReturn(
                $this->prophesize(Adapter::class)->reveal()
            );

        $callback = static function () {
            return new ConcreteAdapterAwareObject();
        };

        /** @var ConcreteAdapterAwareObject $result */
        $result = (new AdapterServiceDelegator())(
            $container->reveal(),
            'name',
            $callback
        );

        $this->assertInstanceOf(
            AdapterInterface::class,
            $result->getAdapter()
        );
    }

    public function testSetAdapterShouldBeCalledForOnlyConcreteAdapter() : void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(AdapterInterface::class)
            ->shouldBeCalledOnce()
            ->willReturn(true);
        $container->get(AdapterInterface::class)
            ->shouldBeCalledOnce()
            ->willReturn(
                $this->prophesize(AdapterInterface::class)->reveal()
            );

        $callback = static function () {
            return new ConcreteAdapterAwareObject();
        };

        /** @var ConcreteAdapterAwareObject $result */
        $result = (new AdapterServiceDelegator())(
            $container->reveal(),
            'name',
            $callback
        );

        $this->assertNull($result->getAdapter());
    }

    public function testSetAdapterShouldNotBeCalledForMissingAdapter() : void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(AdapterInterface::class)
            ->shouldBeCalledOnce()
            ->willReturn(false);
        $container->get(Argument::any())->shouldNotBeCalled();

        $callback = static function () {
            return new ConcreteAdapterAwareObject();
        };

        /** @var ConcreteAdapterAwareObject $result */
        $result = (new AdapterServiceDelegator())(
            $container->reveal(),
            'name',
            $callback
        );

        $this->assertNull($result->getAdapter());
    }

    public function testSetAdapterShouldNotBeCalledForWrongClassInstance(
    ) : void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(Argument::any())->shouldNotBeCalled();

        $callback = static function () {
            return new stdClass();
        };

        $result = (new AdapterServiceDelegator())(
            $container->reveal(),
            'name',
            $callback
        );

        $this->assertNotInstanceOf(AdapterAwareInterface::class, $result);
    }

    public function testDelegatorWithServiceManager()
    {
        $databaseAdapter = new Adapter(
            $this->prophesize(DriverInterface::class)->reveal()
        );

        $container = new ServiceManager([
            'invokables' => [
                ConcreteAdapterAwareObject::class => ConcreteAdapterAwareObject::class,
            ],
            'factories'  => [
                AdapterInterface::class => static function () use (
                    $databaseAdapter
                ) {
                    return $databaseAdapter;
                },
            ],
            'delegators' => [
                ConcreteAdapterAwareObject::class => [
                    AdapterServiceDelegator::class,
                ],
            ],
        ]);

        /** @var ConcreteAdapterAwareObject $result */
        $result = $container->get(ConcreteAdapterAwareObject::class);

        $this->assertInstanceOf(
            AdapterInterface::class,
            $result->getAdapter()
        );
    }

    public function testDelegatorWithServiceManagerAndCustomAdapterName()
    {
        $databaseAdapter = new Adapter(
            $this->prophesize(DriverInterface::class)->reveal()
        );

        $container = new ServiceManager([
            'invokables' => [
                ConcreteAdapterAwareObject::class => ConcreteAdapterAwareObject::class,
            ],
            'factories'  => [
                'alternate-database-adapter' => static function () use (
                    $databaseAdapter
                ) {
                    return $databaseAdapter;
                },
            ],
            'delegators' => [
                ConcreteAdapterAwareObject::class => [
                    new AdapterServiceDelegator('alternate-database-adapter'),
                ],
            ],
        ]);

        /** @var ConcreteAdapterAwareObject $result */
        $result = $container->get(ConcreteAdapterAwareObject::class);

        $this->assertInstanceOf(
            AdapterInterface::class,
            $result->getAdapter()
        );
    }

    public function testDelegatorWithPluginManager()
    {
        $databaseAdapter = new Adapter(
            $this->prophesize(DriverInterface::class)->reveal()
        );

        $container           = new ServiceManager([
            'factories' => [
                AdapterInterface::class => static function () use (
                    $databaseAdapter
                ) {
                    return $databaseAdapter;
                },
            ],
        ]);
        $pluginManagerConfig = [
            'invokables' => [
                ConcreteAdapterAwareObject::class => ConcreteAdapterAwareObject::class,
            ],
            'delegators' => [
                ConcreteAdapterAwareObject::class => [
                    AdapterServiceDelegator::class,
                ],
            ],
        ];

        /** @var AbstractPluginManager $pluginManager */
        $pluginManager = new class($container, $pluginManagerConfig)
            extends AbstractPluginManager {
        };

        $options = [
            'table' => 'foo',
            'field' => 'bar',
        ];

        /** @var ConcreteAdapterAwareObject $result */
        $result = $pluginManager->get(
            ConcreteAdapterAwareObject::class,
            $options
        );

        $this->assertInstanceOf(
            AdapterInterface::class,
            $result->getAdapter()
        );
        $this->assertSame($options, $result->getOptions());
    }
}
