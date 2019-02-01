<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Feature;

use Zend\Db\Adapter\Driver\DriverInterface;

abstract class AbstractFeature
{
    /**
     * @var DriverInterface
     */
    protected $driver = null;

    public function setDriver(DriverInterface $driver): void
    {
        $this->driver = $driver;
    }

    abstract public function getName(): string;
}
