<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use PHPUnit_Framework_TestCase as TestCase;

class AdapterAwareTraitTest extends TestCase
{
    public function testSetDbAdapter()
    {
        $object = $this->getObjectForTrait('\Laminas\Db\Adapter\AdapterAwareTrait');

        $this->assertAttributeEquals(null, 'adapter', $object);

        $driver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $platform = $this->getMock('Laminas\Db\Adapter\Platform\PlatformInterface');

        $adapter = new Adapter($driver, $platform);

        $object->setDbAdapter($adapter);

        $this->assertAttributeEquals($adapter, 'adapter', $object);
    }
}
