<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Platform\Mysql;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Adapter\StatementContainerInterface;
use Laminas\Db\Sql\Platform\PlatformDecoratorInterface;
use Laminas\Db\Sql\Select;

class SelectDecorator extends Select implements PlatformDecoratorInterface
{
    /**
     * @var Select
     */
    protected $select = null;

    /**
     * @param Select $select
     */
    public function setSubject($select)
    {
        $this->select = $select;
    }

    /**
     * @param Adapter $adapter
     * @param StatementContainerInterface $statementContainer
     */
    public function prepareStatement(Adapter $adapter, StatementContainerInterface $statementContainer)
    {
        // localize variables
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }
        parent::prepareStatement($adapter, $statementContainer);
    }

    /**
     * @param PlatformInterface $platform
     * @return string
     */
    public function getSqlString(PlatformInterface $platform = null)
    {
        // localize variables
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }
        return parent::getSqlString($platform);
    }

    protected function processLimit(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null)
    {
        if ($this->limit === null) {
            return null;
        }
        if ($adapter) {
            $driver = $adapter->getDriver();
            $sql = $driver->formatParameterName('limit');
            $parameterContainer->offsetSet('limit', $this->limit, ParameterContainer::TYPE_INTEGER);
        } else {
            $sql = $this->limit;
        }

        return array($sql);
    }

    protected function processOffset(PlatformInterface $platform, Adapter $adapter = null, ParameterContainer $parameterContainer = null)
    {
        if ($this->offset === null) {
            return null;
        }
        if ($adapter) {
            $parameterContainer->offsetSet('offset', $this->offset, ParameterContainer::TYPE_INTEGER);
            return array($adapter->getDriver()->formatParameterName('offset'));
        }

        return array($this->offset);
    }
}
