<?php

namespace Laminas\Db\TableGateway\Feature;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;

class MasterSlaveFeature extends AbstractFeature
{
    /**
     * @var AdapterInterface
     */
    protected $slaveAdapter = null;

    /**
     * @var Sql
     */
    protected $masterSql = null;

    /**
     * @var Sql
     */
    protected $slaveSql = null;

    /**
     * Constructor
     *
     * @param AdapterInterface $slaveAdapter
     * @param Sql|null $slaveSql
     */
    public function __construct(AdapterInterface $slaveAdapter, Sql $slaveSql = null)
    {
        $this->slaveAdapter = $slaveAdapter;
        if ($slaveSql) {
            $this->slaveSql = $slaveSql;
        }
    }

    public function getSlaveAdapter()
    {
        return $this->slaveAdapter;
    }

    /**
     * @return Sql
     */
    public function getSlaveSql()
    {
        return $this->slaveSql;
    }

    /**
     * after initialization, retrieve the original adapter as "master"
     */
    public function postInitialize()
    {
        $this->masterSql = $this->tableGateway->sql;
        if ($this->slaveSql === null) {
            $this->slaveSql = new Sql(
                $this->slaveAdapter,
                $this->tableGateway->sql->getTable(),
                $this->tableGateway->sql->getSqlPlatform()
            );
        }
    }

    /**
     * preSelect()
     * Replace adapter with slave temporarily
     */
    public function preSelect()
    {
        $this->tableGateway->sql = $this->slaveSql;
    }

    /**
     * postSelect()
     * Ensure to return to the master adapter
     */
    public function postSelect()
    {
        $this->tableGateway->sql = $this->masterSql;
    }
}
