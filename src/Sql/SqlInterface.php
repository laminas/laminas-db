<?php

namespace Laminas\Db\Sql;

use Laminas\Db\Adapter\Platform\PlatformInterface;

interface SqlInterface
{
    /**
     * Get SQL string for statement
     *
     * @param null|PlatformInterface $adapterPlatform
     *
     * @return string
     */
    public function getSqlString(PlatformInterface $adapterPlatform = null);
}
