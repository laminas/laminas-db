<?php

namespace Laminas\Db\Metadata\Source;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Exception\InvalidArgumentException;
use Laminas\Db\Metadata\MetadataInterface;

/**
 * Source metadata factory.
 */
class Factory
{
    /**
     * Create source from adapter
     *
     * @return MetadataInterface
     * @throws InvalidArgumentException If adapter platform name not recognized.
     */
    public static function createSourceFromAdapter(Adapter $adapter)
    {
        $platformName = $adapter->getPlatform()->getName();

        switch ($platformName) {
            case 'MySQL':
                return new MysqlMetadata($adapter);
            case 'SQLServer':
                return new SqlServerMetadata($adapter);
            case 'SQLite':
                return new SqliteMetadata($adapter);
            case 'PostgreSQL':
                return new PostgresqlMetadata($adapter);
            case 'Oracle':
                return new OracleMetadata($adapter);
            default:
                throw new InvalidArgumentException("Unknown adapter platform '{$platformName}'");
        }
    }
}
