<?php

namespace Laminas\Db\Adapter;

interface AdapterAwareInterface
{
    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     * @return AdapterAwareInterface
     */
    public function setDbAdapter(Adapter $adapter);
}
