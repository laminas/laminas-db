<?php

namespace Laminas\Db\Adapter\Driver\Pdo\Feature;

use Closure;
use Laminas\Db\Adapter\Driver\Feature\AbstractFeature;
use Laminas\Db\Adapter\Driver\Pdo;

use function stripos;

/**
 * OracleRowCounter
 */
class OracleRowCounter extends AbstractFeature
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'OracleRowCounter';
    }

    /**
     * @return int
     */
    public function getCountForStatement(Pdo\Statement $statement)
    {
        $countStmt = clone $statement;
        $sql       = $statement->getSql();
        if ($sql === '' || stripos($sql, 'select') === false) {
            return;
        }
        $countSql = 'SELECT COUNT(*) as "count" FROM (' . $sql . ')';
        $countStmt->prepare($countSql);
        $result   = $countStmt->execute();
        $countRow = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
        unset($statement, $result);
        return $countRow['count'];
    }

    /**
     * @param string $sql
     * @return null|int
     */
    public function getCountForSql($sql)
    {
        if (stripos($sql, 'select') === false) {
            return;
        }
        $countSql = 'SELECT COUNT(*) as count FROM (' . $sql . ')';
        /** @var \PDO $pdo */
        $pdo      = $this->driver->getConnection()->getResource();
        $result   = $pdo->query($countSql);
        $countRow = $result->fetch(\PDO::FETCH_ASSOC);
        return $countRow['count'];
    }

    /**
     * @param Pdo\Statement|string $context
     * @return Closure
     */
    public function getRowCountClosure($context)
    {
        return function () use ($context) {
            return $context instanceof Pdo\Statement
                ? $this->getCountForStatement($context)
                : $this->getCountForSql($context);
        };
    }
}
