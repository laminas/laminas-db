<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter;

use Laminas\ServiceManager\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Database adapter abstract service factory.
 *
 * Allows configuring several database instances (such as writer and reader).
 */
class AdapterAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Can we create an adapter by the requested name?
     *
     * @param  ServiceLocatorInterface $services
     * @param  string $name
     * @param  string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        $config = $this->getConfig($services);
        if (empty($config)) {
            return false;
        }

        return (
            isset($config[$requestedName])
            && is_array($config[$requestedName])
            && !empty($config[$requestedName])
        );
    }

    /**
     * Create a DB adapter
     *
     * @param  ServiceLocatorInterface $services
     * @param  string $name
     * @param  string $requestedName
     * @return Adapter
     */
    public function createServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        $config = $this->getConfig($services);
        return new Adapter($config[$requestedName]);
    }

    /**
     * Get db configuration, if any
     *
     * @param  ServiceLocatorInterface $services
     * @return array
     */
    protected function getConfig(ServiceLocatorInterface $services)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$services->has('Config')) {
            $this->config = [];
            return $this->config;
        }

        $config = $services->get('Config');
        if (!isset($config['db'])
            || !is_array($config['db'])
        ) {
            $this->config = [];
            return $this->config;
        }

        $config = $config['db'];
        if (!isset($config['adapters'])
            || !is_array($config['adapters'])
        ) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config['adapters'];
        return $this->config;
    }
}
