<?php
///**
// * Laminas (https://getlaminas.org/)
// *
// * @link      http://github.com/laminas/laminas for the canonical source repository
// * @copyright Copyright (c) 2005-2014 Laminas (https://www.zend.com)
// * @license   https://getlaminas.org/license/new-bsd New BSD License
// */
//
//namespace LaminasTest\Db\Adapter\Driver\Pdo;
//
//abstract class AbstractIntegrationTest extends \PHPUnit_Framework_TestCase
//{
//
//    /**
//     * Sets up the fixture, for example, opens a network connection.
//     * This method is called before a test is executed.
//     */
//    protected function setUp()
//    {
//        foreach ($this->variables as $name => $value) {
//            if (!isset($GLOBALS[$value])) {
//                $this->fail('Missing required variable ' . $value . ' from phpunit.xml for this integration test');
//            }
//            $this->variables[$name] = $GLOBALS[$value];
//        }
//
//        if (!extension_loaded('sqlsrv')) {
//            $this->fail('The phpunit group integration-sqlsrv was enabled, but the extension is not loaded.');
//        }
//    }
//}
