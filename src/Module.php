<?php

namespace Laminas\Db;

class Module
{
    /**
     * Retrieve default laminas-db configuration for laminas-mvc context.
     *
     * @return array
     */
    public function getConfig()
    {
        $provider = new ConfigProvider();
        return [
            'service_manager' => $provider->getDependencyConfig(),
        ];
    }
}
