<?php

namespace Laminas\Db\Adapter;

interface StatementContainerInterface
{
    /**
     * Set sql
     *
     * @param $sql
     * @return mixed
     */
    public function setSql($sql);

    /**
     * Get sql
     *
     * @return mixed
     */
    public function getSql();

    /**
     * Set parameter container
     *
     * @param ParameterContainer $parameterContainer
     * @return mixed
     */
    public function setParameterContainer(ParameterContainer $parameterContainer);

    /**
     * Get parameter container
     *
     * @return mixed
     */
    public function getParameterContainer();
}
