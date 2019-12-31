<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Adapter
 */
interface StatementContainerInterface
{
    /**
     * @abstract
     * @param $sql
     * @return mixed
     */
    public function setSql($sql);

    /**
     * @abstract
     * @return mixed
     */
    public function getSql();

    /**
     * @abstract
     * @return mixed
     */
    public function setParameterContainer(ParameterContainer $parameterContainer);

    /**
     * @abstract
     * @return mixed
     */
    public function getParameterContainer();
}
