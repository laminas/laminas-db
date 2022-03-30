<?php

namespace LaminasTest\Db\TestAsset;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Sql\Insert;

class Replace extends Insert
{
    public const SPECIFICATION_INSERT = 'replace';

    /** @var array<string, string> */
    protected $specifications = [
        self::SPECIFICATION_INSERT => 'REPLACE INTO %1$s (%2$s) VALUES (%3$s)',
        self::SPECIFICATION_SELECT => 'REPLACE INTO %1$s %2$s %3$s',
    ];

    /** @return null|string */
    protected function processreplace(
        PlatformInterface $platform,
        ?DriverInterface $driver = null,
        ?ParameterContainer $parameterContainer = null
    ) {
        return parent::processInsert($platform, $driver, $parameterContainer);
    }
}
