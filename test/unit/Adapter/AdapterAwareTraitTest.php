<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use PHPUnit\Framework\TestCase;

class AdapterAwareTraitTest extends TestCase
{
    public function testSetDbAdapter()
    {
        $object = $this->getObjectForTrait(AdapterAwareTrait::class);

        self::assertNull(
            (function ($object) {
                return $object->adapter;
            })->bindTo($object, $object)($object)
        );

        $driver   = $this->getMockBuilder(DriverInterface::class)->getMock();
        $platform = $this->getMockBuilder(PlatformInterface::class)->getMock();

        $adapter = new Adapter($driver, $platform);

        $object->setDbAdapter($adapter);

        self::assertEquals(
            $adapter,
            (function ($object) {
                return $object->adapter;
            })->bindTo($object, $object)($object)
        );
    }
}
