<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Platform\Sqlite;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Sql\Platform\PlatformDecoratorInterface;
use Laminas\Db\Sql\Select;

class SelectDecorator extends Select implements PlatformDecoratorInterface
{
    /**
     * @var Select
     */
    protected $subject = null;

    /**
     * Set Subject
     *
     * @param Select $select
     * @return self
     */
    public function setSubject($select)
    {
        $this->subject = $select;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function localizeVariables()
    {
        parent::localizeVariables();
        $this->specifications[self::COMBINE] = '%1$s %2$s';
    }

    /**
     * {@inheritDoc}
     */
    protected function processStatementStart(PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null)
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    protected function processStatementEnd(PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null)
    {
        return '';
    }
}
