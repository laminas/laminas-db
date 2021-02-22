<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

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
