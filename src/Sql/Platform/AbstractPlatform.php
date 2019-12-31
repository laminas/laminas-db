<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Platform;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Adapter\StatementContainerInterface;
use Laminas\Db\Sql\Exception;
use Laminas\Db\Sql\PreparableSqlInterface;
use Laminas\Db\Sql\SqlInterface;

class AbstractPlatform implements PlatformDecoratorInterface, PreparableSqlInterface, SqlInterface
{
    /**
     * @var object
     */
    protected $subject = null;

    /**
     * @var PlatformDecoratorInterface[]
     */
    protected $decorators = array();

    /**
     * @param $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param $type
     * @param PlatformDecoratorInterface $decorator
     */
    public function setTypeDecorator($type, PlatformDecoratorInterface $decorator)
    {
        $this->decorators[$type] = $decorator;
    }

    /**
     * @return array|PlatformDecoratorInterface[]
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * @param Adapter $adapter
     * @param StatementContainerInterface $statementContainer
     * @throws Exception\RuntimeException
     * @return void
     */
    public function prepareStatement(Adapter $adapter, StatementContainerInterface $statementContainer)
    {
        if (!$this->subject instanceof PreparableSqlInterface) {
            throw new Exception\RuntimeException('The subject does not appear to implement Laminas\Db\Sql\PreparableSqlInterface, thus calling prepareStatement() has no effect');
        }

        $decoratorForType = false;
        foreach ($this->decorators as $type => $decorator) {
            if ($this->subject instanceof $type && $decorator instanceof PreparableSqlInterface) {
                /** @var $decoratorForType PreparableSqlInterface|PlatformDecoratorInterface */
                $decoratorForType = $decorator;
                break;
            }
        }
        if ($decoratorForType) {
            $decoratorForType->setSubject($this->subject);
            $decoratorForType->prepareStatement($adapter, $statementContainer);
        } else {
            $this->subject->prepareStatement($adapter, $statementContainer);
        }
    }

    /**
     * @param null|\Laminas\Db\Adapter\Platform\PlatformInterface $adapterPlatform
     * @return mixed
     * @throws Exception\RuntimeException
     */
    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        if (!$this->subject instanceof SqlInterface) {
            throw new Exception\RuntimeException('The subject does not appear to implement Laminas\Db\Sql\PreparableSqlInterface, thus calling prepareStatement() has no effect');
        }

        $decoratorForType = false;
        foreach ($this->decorators as $type => $decorator) {
            if ($this->subject instanceof $type && $decorator instanceof SqlInterface) {
                /** @var $decoratorForType SqlInterface|PlatformDecoratorInterface */
                $decoratorForType = $decorator;
                break;
            }
        }
        if ($decoratorForType) {
            $decoratorForType->setSubject($this->subject);
            return $decoratorForType->getSqlString($adapterPlatform);
        }

        return $this->subject->getSqlString($adapterPlatform);
    }
}
