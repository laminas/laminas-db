<?php

namespace LaminasTest\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use LaminasTest\Db\DeprecatedAssertionsTrait;
use PHPUnit\Framework\TestCase;

class AdapterAwareTraitTest extends TestCase
{
    use DeprecatedAssertionsTrait;

    public function testSetDbAdapter()
    {
        $object = $this->getObjectForTrait(AdapterAwareTrait::class);

        self::assertAttributeEquals(null, 'adapter', $object);

        $driver   = $this->getMockBuilder(DriverInterface::class)->getMock();
        $platform = $this->getMockBuilder(PlatformInterface::class)->getMock();

        $adapter = new Adapter($driver, $platform);

        $object->setDbAdapter($adapter);

        self::assertAttributeEquals($adapter, 'adapter', $object);
    }
}
