<?php

namespace Laminas\Db;

class ConfigProvider
{
    /**
     * Retrieve laminas-db default configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Retrieve laminas-db default dependency configuration.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'abstract_factories' => [
                Adapter\AdapterAbstractServiceFactory::class,
            ],
            'factories'          => [
                Adapter\AdapterInterface::class => Adapter\AdapterServiceFactory::class,
            ],
            'aliases'            => [
                Adapter\Adapter::class => Adapter\AdapterInterface::class,

                // Legacy Zend Framework aliases
                // phpcs:disable WebimpressCodingStandard.Formatting.StringClassReference.Found
                'Zend\Db\Adapter\AdapterInterface' => Adapter\AdapterInterface::class,
                'Zend\Db\Adapter\Adapter'          => Adapter\Adapter::class,
                // phpcs:enable WebimpressCodingStandard.Formatting.StringClassReference.Found
            ],
        ];
    }
}
