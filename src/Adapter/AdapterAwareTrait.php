<?php

namespace Laminas\Db\Adapter;

trait AdapterAwareTrait
{
    /**
     * @var Adapter
     */
    protected $adapter = null;

    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     * @return self Provides a fluent interface
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}
