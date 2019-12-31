<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Driver;

use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\StatementContainerInterface;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Adapter
 */
interface StatementInterface extends StatementContainerInterface
{

    /**
     * @return resource
     */
    public function getResource();

    /**
     * @abstract
     * @param string $sql
     */
    public function prepare($sql = null);

    /**
     * @abstract
     * @return bool
     */
    public function isPrepared();

    /**
     * @abstract
     * @param null $parameters
     * @return ResultInterface
     */
    public function execute($parameters = null);

}
