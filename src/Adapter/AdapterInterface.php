<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter;

use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Platform\PlatformInterface;

interface AdapterInterface
{
    public function getDriver(): DriverInterface;

    public function getPlatform(): ?PlatformInterface;
}
