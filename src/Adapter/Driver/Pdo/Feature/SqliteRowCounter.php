<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Driver\Pdo\Feature;

use Laminas\Db\Adapter\Driver\Feature\AbstractFeature;
use Laminas\Db\Adapter\Driver\Pdo;

/**
 * SqliteRowCounter
 *
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Adapter
 */
class SqliteRowCounter extends AbstractFeature
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'SqliteRowCounter';
    }

    /**
     * @param \Laminas\Db\Adapter\Driver\Pdo\Statement $statement
     * @return int
     */
    public function getCountForStatement(Pdo\Statement $statement)
    {
        $countStmt = clone $statement;
        $sql = $statement->getSql();
        if ($sql == '' || stripos($sql, 'select') === false) {
            return null;
        }
        $countSql = 'SELECT COUNT(*) as "count" FROM (' . $sql . ')';
        $countStmt->prepare($countSql);
        $result = $countStmt->execute();
        $countRow = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
        unset($statement, $result);
        return $countRow['count'];
    }

    /**
     * @param $sql
     * @return null|int
     */
    public function getCountForSql($sql)
    {
        if (!stripos($sql, 'select')) {
            return null;
        }
        $countSql = 'SELECT COUNT(*) as count FROM (' . $sql . ')';
        /** @var $pdo \PDO */
        $pdo = $this->pdoDriver->getConnection()->getResource();
        $result = $pdo->query($countSql);
        $countRow = $result->fetch(\PDO::FETCH_ASSOC);
        return $countRow['count'];
    }

    /**
     * @param $context
     * @return closure
     */
    public function getRowCountClosure($context)
    {
        $sqliteRowCounter = $this;
        return function () use ($sqliteRowCounter, $context) {
            /** @var $sqliteRowCounter SqliteRowCounter */
            return ($context instanceof Pdo\Statement)
                ? $sqliteRowCounter->getCountForStatement($context)
                : $sqliteRowCounter->getCountForSql($context);
        };
    }

}
