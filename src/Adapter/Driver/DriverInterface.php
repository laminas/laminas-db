<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Driver;

interface DriverInterface
{
    const PARAMETERIZATION_POSITIONAL = 'positional';
    const PARAMETERIZATION_NAMED = 'named';
    const NAME_FORMAT_CAMELCASE = 'camelCase';
    const NAME_FORMAT_NATURAL = 'natural';

    public function getDatabasePlatformName(
        string $nameFormat = self::NAME_FORMAT_CAMELCASE
    ): string;

    public function checkEnvironment(): void;

    public function getConnection(): ConnectionInterface;

    /**
     * @param mixed $resource
     */
    public function createStatement($resource = null): StatementInterface;

    /**
     * @param mixed $resource
     */
    public function createResult($resource): ResultInterface;

    public function getPrepareType(): string;

    /**
     * @param mixed  $type
     */
    public function formatParameterName(string $name, $type = null): string;

    public function getLastGeneratedValue(): string;
}
