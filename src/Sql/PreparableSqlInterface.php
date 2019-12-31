<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\StatementContainerInterface;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Sql
 */
interface PreparableSqlInterface
{

    /**
     * @param Adapter $adapter
     * @param StatementContainerInterface
     * @return void
     */
    public function prepareStatement(Adapter $adapter, StatementContainerInterface $statementContainer);
}
