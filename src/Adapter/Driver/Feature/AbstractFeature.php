<?php

namespace Laminas\Db\Adapter\Driver\Feature;

use Laminas\Db\Adapter\Driver\DriverInterface;

abstract class AbstractFeature
{
    /** @var DriverInterface */
    protected $driver;

    /**
     * Set driver
     *
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
