<?php

namespace Laminas\Db\Adapter;

interface StatementContainerInterface
{
    /**
     * Set sql
     *
     * @param null|string $sql
     * @return static
     */
    public function setSql($sql);

    /**
     * Get sql
     *
     * @return null|string
     */
    public function getSql();

    /**
     * Set parameter container
     *
     * @return static
     */
    public function setParameterContainer(ParameterContainer $parameterContainer);

    /**
     * Get parameter container
     *
     * @return null|ParameterContainer
     */
    public function getParameterContainer();
}
