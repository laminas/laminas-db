<?php

namespace Laminas\Db\Adapter;

class StatementContainer implements StatementContainerInterface
{
    /** @var string */
    protected $sql = '';

    /** @var ParameterContainer */
    protected $parameterContainer;

    /**
     * @param string|null $sql
     */
    public function __construct($sql = null, ?ParameterContainer $parameterContainer = null)
    {
        if ($sql) {
            $this->setSql($sql);
        }
        $this->parameterContainer = $parameterContainer ?: new ParameterContainer();
    }

    /**
     * @param string $sql
     * @return $this Provides a fluent interface
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return $this Provides a fluent interface
     */
    public function setParameterContainer(ParameterContainer $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    /**
     * @return null|ParameterContainer
     */
    public function getParameterContainer()
    {
        return $this->parameterContainer;
    }
}
