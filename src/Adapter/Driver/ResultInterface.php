<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Driver;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Adapter
 */
interface ResultInterface extends \Countable, \Iterator
{
    public function buffer();
    public function isBuffered();
    public function isQueryResult();
    public function getAffectedRows();
    public function getGeneratedValue();
    public function getResource();
    public function getFieldCount();
}
