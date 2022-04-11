<?php

namespace Laminas\Db\Sql;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\Platform\PlatformInterface;

use function is_array;
use function is_string;
use function sprintf;

class Sql
{
    /** @var AdapterInterface */
    protected $adapter;

    /** @var string|array|TableIdentifier */
    protected $table;

    /** @var Platform\Platform */
    protected $sqlPlatform;

    /**
     * @param null|string|array|TableIdentifier $table
     * @param null|Platform\AbstractPlatform    $sqlPlatform @deprecated since version 3.0
     */
    public function __construct(
        AdapterInterface $adapter,
        $table = null,
        ?Platform\AbstractPlatform $sqlPlatform = null
    ) {
        $this->adapter = $adapter;
        if ($table) {
            $this->setTable($table);
        }
        $this->sqlPlatform = $sqlPlatform ?: new Platform\Platform($adapter);
    }

    /**
     * @return null|AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /** @return bool */
    public function hasTable()
    {
        return $this->table !== null;
    }

    /**
     * @param string|array|TableIdentifier $table
     * @return $this Provides a fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function setTable($table)
    {
        if (is_string($table) || is_array($table) || $table instanceof TableIdentifier) {
            $this->table = $table;
        } else {
            throw new Exception\InvalidArgumentException(
                'Table must be a string, array or instance of TableIdentifier.'
            );
        }
        return $this;
    }

    /** @return string|array|TableIdentifier */
    public function getTable()
    {
        return $this->table;
    }

    /** @return Platform\Platform */
    public function getSqlPlatform()
    {
        return $this->sqlPlatform;
    }

    /**
     * @param null|string|TableIdentifier $table
     * @return Select
     */
    public function select($table = null)
    {
        if ($this->table !== null && $table !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }
        return new Select($table ?: $this->table);
    }

    /**
     * @param null|string|TableIdentifier $table
     * @return Insert
     */
    public function insert($table = null)
    {
        if ($this->table !== null && $table !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }
        return new Insert($table ?: $this->table);
    }

    /**
     * @param null|string|TableIdentifier $table
     * @return Update
     */
    public function update($table = null)
    {
        if ($this->table !== null && $table !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }
        return new Update($table ?: $this->table);
    }

    /**
     * @param null|string|TableIdentifier $table
     * @return Delete
     */
    public function delete($table = null)
    {
        if ($this->table !== null && $table !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }
        return new Delete($table ?: $this->table);
    }

    /**
     * @return StatementInterface
     */
    public function prepareStatementForSqlObject(
        PreparableSqlInterface $sqlObject,
        ?StatementInterface $statement = null,
        ?AdapterInterface $adapter = null
    ) {
        $adapter   = $adapter ?: $this->adapter;
        $statement = $statement ?: $adapter->getDriver()->createStatement();

        return $this->sqlPlatform->setSubject($sqlObject)->prepareStatement($adapter, $statement);
    }

    /**
     * Get sql string using platform or sql object
     *
     * @deprecated Deprecated in 2.4. Use buildSqlString() instead
     *
     * @return string
     */
    public function getSqlStringForSqlObject(SqlInterface $sqlObject, ?PlatformInterface $platform = null)
    {
        $platform = $platform ?: $this->adapter->getPlatform();
        return $this->sqlPlatform->setSubject($sqlObject)->getSqlString($platform);
    }

    /**
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function buildSqlString(SqlInterface $sqlObject, ?AdapterInterface $adapter = null)
    {
        return $this
            ->sqlPlatform
            ->setSubject($sqlObject)
            ->getSqlString($adapter ? $adapter->getPlatform() : $this->adapter->getPlatform());
    }
}
