<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter;

use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;

class AdapterAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Laminas\ServiceManager\ServiceLocatorInterface
     */
    private $serviceManager;

    /**
     * Set up service manager and database configuration.
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->serviceManager = new ServiceManager(new ServiceManagerConfig(array(
            'abstract_factories' => array('Laminas\Db\Adapter\AdapterAbstractServiceFactory'),
        )));

        $this->serviceManager->setService('Config', array(
            'db' => array(
                'adapters' => array(
                    'Laminas\Db\Adapter\Writer' => array(
                        'driver' => 'mysqli',
                    ),
                    'Laminas\Db\Adapter\Reader' => array(
                        'driver' => 'mysqli',
                    ),
                ),
            ),
        ));
    }

    /**
     * @return array
     */
    public function providerValidService()
    {
        return array(
            array('Laminas\Db\Adapter\Writer'),
            array('Laminas\Db\Adapter\Reader'),
        );
    }

    /**
     * @return array
     */
    public function providerInvalidService()
    {
        return array(
            array('Laminas\Db\Adapter\Unknown'),
        );
    }

    /**
     * @param string $service
     * @dataProvider providerValidService
     */
    public function testValidService($service)
    {
        $actual = $this->serviceManager->get($service);
        $this->assertInstanceOf('Laminas\Db\Adapter\Adapter', $actual);
    }

    /**
     * @param string $service
     * @dataProvider providerInvalidService
     * @expectedException \Laminas\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testInvalidService($service)
    {
        $actual = $this->serviceManager->get($service);
    }
}
