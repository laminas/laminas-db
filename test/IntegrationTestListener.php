<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db;

use Exception;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestListener;
use PHPUnit_Framework_TestSuite;

class IntegrationTestListener implements PHPUnit_Framework_TestListener
{
    protected $adapters = array(
        'mysqli' => null,
        'pdo_mysql' => null,
        'pgsql' => null,
        'pdo_pgsql' => null,
        'pdo_sqlite' => null,
        'sqlsrv' => null,
        'pdo_sqlsrv' => null,
    );

    public function __construct()
    {
        if (isset($GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME'])) {
            if (extension_loaded('mysqli')) {
                $this->adapters['mysqli'] = new \mysqli(
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME'],
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_MYSQL_USERNAME'],
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PASSWORD'],
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE']
                );
            }
            if (extension_loaded('pdo')) {
                $this->adapters['pdo_mysql'] = new \Pdo(
                    'mysql:host=' . $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME'] . ';dbname=' . $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_MYSQL_DATABASE'],
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_MYSQL_USERNAME'],
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_MYSQL_PASSWORD']
                );
            }
        }
        if (isset($GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME'])) {
            if (extension_loaded('pgsql')) {
                $this->adapters['pgsql'] = pg_connect(
                    'host=' . $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME']
                        . ' dbname=' . $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE']
                        . ' user=' . $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_PGSQL_USERNAME']
                        . ' password=' . $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_PGSQL_PASSWORD']
                );
            }
            if (extension_loaded('pdo')) {
                $this->adapters['pdo_pgsql'] = new \Pdo(
                    'pgsql:host=' . $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME'] . ';dbname=' . $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_PGSQL_DATABASE'],
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_PGSQL_USERNAME'],
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_PGSQL_PASSWORD']
                );
            }
        }
        if (isset($GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLITE_MEMORY'])) {
            if (extension_loaded('pdo')) {
                $this->adapters['pdo_sqlite'] = new \Pdo(
                    'sqlite::memory:'
                );
            }
        }
        if (isset($GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME'])) {
            if (extension_loaded('sqlsrv')) {
                $this->adapters['sqlsrv'] = sqlsrv_connect(
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME'],
                    array(
                        'UID' => $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME'],
                        'PWD' => $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD'],
                        'Database' => (isset($GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE'])
                            ? $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE'] : null)
                    )
                );
                if (!$this->adapters['sqlsrv']) {
                    var_dump(sqlsrv_errors());
                    exit;
                }
            }
            if (extension_loaded('pdo')) {
                $this->adapters['pdo_sqlsrv'] = new \Pdo(
                    'sqlsrv:Server=' . $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_HOSTNAME']
                        . ';Database=' . (isset($GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE'])
                            ? $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_DATABASE'] : null),
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_USERNAME'],
                    $GLOBALS['LAMINAS_DB_ADAPTER_DRIVER_SQLSRV_PASSWORD']
                );
            }
        }
    }

    /** methods required for the listener interface compliance */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }
    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    } // Support PHPUnit 3.8+

    /**
     * A test started.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        /** @var $test \PHPUnit_Framework_TestCase */
        $testcase = get_class($test);
        if (strpos($testcase, 'LaminasTest\Db') === 0 && strpos($testcase, 'Integration')) {
            $refObj = new \ReflectionObject($test);
            if ($refObj->hasProperty('adapters')) {
                $refProp = $refObj->getProperty('adapters');
                $refProp->setAccessible(true);
                $refProp->setValue($test, $this->adapters);
            }
        }
    }

    /**
     * A test ended.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        /** @var $test \PHPUnit_Framework_TestCase */
        $testcase = get_class($test);
        if (strpos($testcase, 'LaminasTest\Db') === 0 && strpos($testcase, 'Integration')) {
            $refObj = new \ReflectionObject($test);
            if ($refObj->hasProperty('adapters')) {
                $refProp = $refObj->getProperty('adapters');
                $refProp->setAccessible(true);
                $refProp->setValue($test, array());
            }
        }
    }
}
