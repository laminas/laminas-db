<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\TableGateway\Feature;

use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Update;
use Laminas\Db\TableGateway\Exception;
use Laminas\EventManager\EventManagerInterface;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage TableGateway
 */
class EventFeature extends AbstractFeature
{

    /**
     * @var EventManagerInterface
     */
    protected $eventManager = null;

    /**
     * @var null
     */
    protected $event = null;

    /**
     * @param EventManagerInterface $eventManager
     * @param EventFeature\TableGatewayEvent $tableGatewayEvent
     */
    public function __construct(EventManagerInterface $eventManager, EventFeature\TableGatewayEvent $tableGatewayEvent)
    {
        $this->eventManager = $eventManager;
        $this->event = ($tableGatewayEvent) ?: new EventFeature\TableGatewayEvent();
    }

    public function preInitialize()
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->eventManager->trigger($this->event);
    }

    public function postInitialize()
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->eventManager->trigger($this->event);
    }

    public function preSelect(Select $select)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('select' => $select));
        $this->eventManager->trigger($this->event);
    }

    public function postSelect(StatementInterface $statement, ResultInterface $result, ResultSetInterface $resultSet)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
            'result_set' => $resultSet
        ));
        $this->eventManager->trigger($this->event);
    }

    public function preInsert(Insert $insert)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('insert' => $insert));
        $this->eventManager->trigger($this->event);
    }

    public function postInsert(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }

    public function preUpdate(Update $update)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('update' => $update));
        $this->eventManager->trigger($this->event);
    }

    public function postUpdate(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }

    public function preDelete(Delete $delete)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('delete' => $delete));
        $this->eventManager->trigger($this->event);
    }

    public function postDelete(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setTarget($this->tableGateway);
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }

}
