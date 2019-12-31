<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Driver\Oci8\Feature;

use Laminas\Db\Adapter\Driver\Feature\AbstractFeature;
use Laminas\Db\Adapter\Driver\Oci8\Statement;

/**
 * Class for count of results of a select
 */
class RowCounter extends AbstractFeature
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'RowCounter';
    }

    public function getCountForStatement(Statement $statement): int
    {
        $countStmt = clone $statement;
        $sql = $statement->getSql();
        if ($sql == '' || stripos(strtolower($sql), 'select') === false) {
            return 0;
        }
        $countSql = 'SELECT COUNT(*) as "count" FROM (' . $sql . ')';
        $countStmt->prepare($countSql);
        $result = $countStmt->execute();
        $countRow = $result->current();
        return $countRow['count'];
    }

    public function getCountForSql(string $sql): int
    {
        if (stripos(strtolower($sql), 'select') === false) {
            return 0;
        }
        $countSql = 'SELECT COUNT(*) as "count" FROM (' . $sql . ')';
        $result = $this->driver->getConnection()->execute($countSql);
        $countRow = $result->current();
        return $countRow['count'];
    }
}
