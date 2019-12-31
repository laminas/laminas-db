<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Driver;

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
