<?php

namespace Laminas\Db\Adapter;

use Psr\Container\ContainerInterface;

class AdapterServiceDelegator
{
    /** @var string */
    private $adapterName;

    public function __construct(string $adapterName = AdapterInterface::class)
    {
        $this->adapterName = $adapterName;
    }

    public static function __set_state(array $state): self
    {
        return new self($state['adapterName'] ?? AdapterInterface::class);
    }

    /** @return AdapterInterface */
    public function __invoke(
        ContainerInterface $container,
        string $name,
        callable $callback,
        ?array $options = null
    ) {
        $instance = $callback();

        if (! $instance instanceof AdapterAwareInterface) {
            return $instance;
        }

        if (! $container->has($this->adapterName)) {
            return $instance;
        }

        $databaseAdapter = $container->get($this->adapterName);

        if (! $databaseAdapter instanceof Adapter) {
            return $instance;
        }

        $instance->setDbAdapter($databaseAdapter);

        return $instance;
    }
}
