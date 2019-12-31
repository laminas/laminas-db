<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

if (! interface_exists(\PHPUnit_Framework_Test::class)) {
    class_alias(\PHPUnit\Framework\Test::class, \PHPUnit_Framework_Test::class);
}

if (! class_exists(\PHPUnit_Framework_AssertionFailedError::class)) {
    class_alias(\PHPUnit\Framework\AssertionFailedError::class, \PHPUnit_Framework_AssertionFailedError::class);
}

if (! class_exists(\PHPUnit_Framework_TestSuite::class)) {
    class_alias(\PHPUnit\Framework\TestSuite::class, \PHPUnit_Framework_TestSuite::class);
}

if (! class_exists(\PHPUnit\Framework\Error\Error::class)) {
    class_alias(\PHPUnit_Framework_Error::class, \PHPUnit\Framework\Error\Error::class);
}

if (! class_exists(\PHPUnit\Framework\Error\Notice::class)) {
    class_alias(\PHPUnit_Framework_Error_Notice::class, \PHPUnit\Framework\Error\Notice::class);
}
