<?php

declare(strict_types=1);

namespace Laminas\Db\Adapter;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerInterface;

class AdapterServiceFactory implements FactoryInterface
{
    /**
     * Create db adapter service
     *
     * @return Adapter
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): mixed
    {
        $config = $container->get('config');
        return new Adapter($config['db']);
    }

    /**
     * Create db adapter service (v2)
     */
    public function createService(ServiceLocatorInterface $container): Adapter
    {
        return $this($container, Adapter::class);
    }
}
