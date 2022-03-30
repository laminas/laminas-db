<?php

namespace Laminas\Db\Sql;

use Laminas\Db\Adapter\Platform\PlatformInterface;

interface SqlInterface
{
    /**
     * Get SQL string for statement
     *
     * @return string
     */
    public function getSqlString(?PlatformInterface $adapterPlatform = null);
}
