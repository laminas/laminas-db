<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\TableGateway\Feature;

use Laminas\Db\Adapter\Adapter;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage TableGateway
 */
class MasterSlaveFeature extends AbstractFeature
{

    /**
     * @var Adapter
     */
    protected $masterAdapter = null;

    /**
     * @var Adapter
     */
    protected $slaveAdapter = null;

    /**
     * Constructor
     *
     * @param Adapter $slaveAdapter
     */
    public function __construct(Adapter $slaveAdapter)
    {
        $this->slaveAdapter = $slaveAdapter;
    }

    /**
     * after initialization, retrieve the original adapter as "master"
     */
    public function postInitialize()
    {
        $this->masterAdapter = $this->tableGateway->adapter;
    }

    /**
     * preSelect()
     * Replace adapter with slave temporarily
     */
    public function preSelect()
    {
        $this->tableGateway->adapter = $this->slaveAdapter;
    }

    /**
     * postSelect()
     * Ensure to return to the master adapter
     */
    public function postSelect()
    {
        $this->tableGateway->adapter = $this->masterAdapter;
    }

}
