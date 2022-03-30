<?php

namespace Laminas\Db\Adapter;

trait AdapterAwareTrait
{
    /** @var Adapter */
    protected $adapter;

    /**
     * Set db adapter
     *
     * @return self Provides a fluent interface
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}
