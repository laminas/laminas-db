<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter;

class StatementContainer implements StatementContainerInterface
{
    /**
     * @var string
     */
    protected $sql = '';

    /**
     * @var ParameterContainer
     */
    protected $parameterContainer;

    public function __construct(string $sql = null, ParameterContainer $parameterContainer = null)
    {
        if ($sql) {
            $this->setSql($sql);
        }
        $this->parameterContainer = ($parameterContainer) ?: new ParameterContainer;
    }

    public function setSql(string $sql): StatementContainerInterface
    {
        $this->sql = $sql;
        return $this;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function setParameterContainer(ParameterContainer $parameterContainer): StatementContainerInterface
    {
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    public function getParameterContainer(): ParameterContainer
    {
        return $this->parameterContainer;
    }
}
