<?php

namespace Laminas\Db\TableGateway;

use Closure;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Where;

interface TableGatewayInterface
{
    /** @return string */
    public function getTable();

    /**
     * @param Where|Closure|string|array $where
     * @return ResultSetInterface
     */
    public function select($where = null);

    /**
     * @param array<string, mixed> $set
     * @return int
     */
    public function insert($set);

    /**
     * @param array<string, mixed> $set
     * @param Where|Closure|string|array $where
     * @return int
     */
    public function update($set, $where = null);

    /**
     * @param Where|Closure|string|array $where
     * @return int
     */
    public function delete($where);
}
