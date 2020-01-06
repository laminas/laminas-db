<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use PHPUnit\Framework\TestCase;

class AdapterAwareTraitTest extends TestCase
{
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
