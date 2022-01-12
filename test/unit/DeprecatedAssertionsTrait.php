<?php

namespace LaminasTest\Db;

use PHPUnit\Framework\Assert;
use ReflectionProperty;

trait DeprecatedAssertionsTrait
{
    /**
     * @param mixed $expected
     */
    public static function assertAttributeEquals(
        $expected,
        string $attribute,
        object $instance,
        string $message = ''
    ): void {
        $r = new ReflectionProperty($instance, $attribute);
        $r->setAccessible(true);
        Assert::assertEquals($expected, $r->getValue($instance), $message);
    }

    /**
     * @return mixed
     */
    public function readAttribute(object $instance, string $attribute)
    {
        $r = new ReflectionProperty($instance, $attribute);
        $r->setAccessible(true);
        return $r->getValue($instance);
    }
}
