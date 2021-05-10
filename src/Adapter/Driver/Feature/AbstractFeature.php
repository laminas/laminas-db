<?php

namespace Laminas\Db\Adapter\Driver\Feature;

use Laminas\Db\Adapter\Driver\DriverInterface;

abstract class AbstractFeature
{
    /**
     * @var DriverInterface
     */
    protected $driver = null;

    /**
     * Set driver
     *
     * @param DriverInterface $driver
     * @return void
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get name
     *
     * @return string
     */
    abstract public function getName();
}
