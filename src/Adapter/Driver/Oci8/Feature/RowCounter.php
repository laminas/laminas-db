<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\Oci8\Feature;

use Zend\Db\Adapter\Driver\Feature\AbstractFeature;
use Zend\Db\Adapter\Driver\Oci8\Statement;

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
