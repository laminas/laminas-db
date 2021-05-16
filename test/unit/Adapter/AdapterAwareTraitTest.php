<?php

namespace LaminasTest\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use LaminasTest\Db\DeprecatedAssertionsTrait;
use PHPUnit\Framework\TestCase;

class AdapterAwareTraitTest extends TestCase
{
    use DeprecatedAssertionsTrait;

    public function testSetDbAdapter()
    {
        $object = $this->getObjectForTrait('\Laminas\Db\Adapter\AdapterAwareTrait');

        self::assertAttributeEquals(null, 'adapter', $object);

        $driver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $platform = $this->getMockBuilder('Laminas\Db\Adapter\Platform\PlatformInterface')->getMock();

        $adapter = new Adapter($driver, $platform);

        $object->setDbAdapter($adapter);

        self::assertAttributeEquals($adapter, 'adapter', $object);
    }
}
