<?php

declare(strict_types=1);

namespace Laminas\Db\Adapter;

use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerInterface;

use function is_array;

/**
 * Database adapter abstract service factory.
 *
 * Allows configuring several database instances (such as writer and reader).
 */
class AdapterAbstractServiceFactory implements AbstractFactoryInterface
{
    /** @var array */
    protected $config;

    /**
     * Can we create an adapter by the requested name?
     */
    public function canCreate(ContainerInterface $container, string $requestedName): bool
    {
        $config = $this->getConfig($container);
        if (empty($config)) {
            return false;
        }

        return isset($config[$requestedName])
            && is_array($config[$requestedName])
            && ! empty($config[$requestedName]);
    }

    /**
     * Determine if we can create a service with name (SM v2 compatibility)
     */
    public function canCreateServiceWithName(
        ServiceLocatorInterface $serviceLocator,
        string $name,
        string $requestedName
    ): bool {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * Create a DB adapter
     *
     * @param  array|null $options
     * @return Adapter
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): mixed
    {
        $config = $this->getConfig($container);
        return new Adapter($config[$requestedName]);
    }

    /**
     * Create service with name
     */
    public function createServiceWithName(
        ServiceLocatorInterface $serviceLocator,
        string $name,
        string $requestedName
    ): Adapter {
        return $this($serviceLocator, $requestedName);
    }

    /**
     * Get db configuration, if any
     */
    protected function getConfig(ContainerInterface $container): array
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (! $container->has('config')) {
            $this->config = [];
            return $this->config;
        }

        $config = $container->get('config');
        if (
            ! isset($config['db'])
            || ! is_array($config['db'])
        ) {
            $this->config = [];
            return $this->config;
        }

        $config = $config['db'];
        if (
            ! isset($config['adapters'])
            || ! is_array($config['adapters'])
        ) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config['adapters'];
        return $this->config;
    }
}
