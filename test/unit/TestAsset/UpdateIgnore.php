<?php

namespace LaminasTest\Db\TestAsset;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Sql\Update;

class UpdateIgnore extends Update
{
    const SPECIFICATION_UPDATE = 'updateIgnore';

    protected $specifications = [
        self::SPECIFICATION_UPDATE => 'UPDATE IGNORE %1$s',
        self::SPECIFICATION_SET => 'SET %1$s',
        self::SPECIFICATION_WHERE  => 'WHERE %1$s',
    ];

    protected function processupdateIgnore(
        PlatformInterface $platform,
        DriverInterface $driver = null,
        ParameterContainer $parameterContainer = null
    ) {
        return parent::processUpdate($platform, $driver, $parameterContainer);
    }
}
