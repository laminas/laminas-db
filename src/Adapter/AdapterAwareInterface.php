<?php

declare(strict_types=1);

namespace Laminas\Db\Adapter;

interface AdapterAwareInterface
{
    /**
     * Set db adapter
     *
     * @return AdapterAwareInterface
     */
    public function setDbAdapter(Adapter $adapter);
}
