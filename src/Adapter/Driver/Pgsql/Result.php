<?php

namespace Laminas\Db\Adapter\Driver\Pgsql;

use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Exception;
use PgSql\Result as PgSqlResult;
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use ReturnTypeWillChange;

use function get_resource_type;
use function is_resource;
use function pg_affected_rows;
use function pg_fetch_assoc;
use function pg_num_fields;
use function pg_num_rows;

class Result implements ResultInterface
{
    /** @var resource */
    protected $resource;

    /** @var int */
    protected $position = 0;

    /** @var int */
    protected $count = 0;

    /** @var null|mixed */
    protected $generatedValue;

    /**
     * Initialize
     *
     * @param resource $resource
     * @param int|string $generatedValue
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function initialize($resource, $generatedValue)
    {
        if (
            ! $resource instanceof PgSqlResult
            && (
                ! is_resource($resource)
                || 'pgsql result' !== get_resource_type($resource)
            )
        ) {
            throw new Exception\InvalidArgumentException('Resource not of the correct type.');
        }

        $this->resource       = $resource;
        $this->count          = pg_num_rows($this->resource);
        $this->generatedValue = $generatedValue;
    }

    /**
     * Current
     *
     * @return array|bool|mixed
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        if ($this->count === 0) {
            return false;
        }
        return pg_fetch_assoc($this->resource, $this->position);
    }

    /**
     * Next
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        $this->position++;
    }

    /**
     * Key
     *
     * @return int|mixed
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    /**
     * Valid
     *
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function valid()
    {
        return $this->position < $this->count;
    }

    /**
     * Rewind
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Buffer
     *
     * @return null
     */
    public function buffer()
    {
        return null;
    }

    /**
     * Is buffered
     *
     * @return false
     */
    public function isBuffered()
    {
        return false;
    }

    /**
     * Is query result
     *
     * @return bool
     */
    public function isQueryResult()
    {
        return pg_num_fields($this->resource) > 0;
    }

    /**
     * Get affected rows
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return pg_affected_rows($this->resource);
    }

    /**
     * Get generated value
     *
     * @return mixed|null
     */
    public function getGeneratedValue()
    {
        return $this->generatedValue;
    }

    /**
     * Get resource
     */
    public function getResource()
    {
        // TODO: Implement getResource() method.
    }

    /**
     * Count
     *
     * @return int The custom count as an integer.
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        return $this->count;
    }

    /**
     * Get field count
     *
     * @return int
     */
    public function getFieldCount()
    {
        return pg_num_fields($this->resource);
    }
}
