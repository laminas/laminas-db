<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver;

interface ConnectionInterface
{
    public function getCurrentSchema(): string;

    public function getResource();

    public function connect(): ConnectionInterface;

    public function isConnected(): bool;

    public function disconnect(): ConnectionInterface;

    public function beginTransaction(): ConnectionInterface;

    public function commit(): ConnectionInterface;

    public function rollback(): ConnectionInterface;

    public function execute(string $sql): ResultInterface;

    public function getLastGeneratedValue(string $name = null): string;
}
