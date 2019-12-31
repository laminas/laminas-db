<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Driver\Pgsql;

use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Exception;

class Result implements ResultInterface
{
    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @var null|mixed
     */
    protected $generatedValue = null;

    /**
     * Initialize
     *
     * @param $resource
     * @param $generatedValue
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function initialize($resource, $generatedValue)
    {
        if (! is_resource($resource) || get_resource_type($resource) != 'pgsql result') {
            throw new Exception\InvalidArgumentException('Resource not of the correct type.');
        }

        $this->resource = $resource;
        $this->count = pg_num_rows($this->resource);
        $this->generatedValue = $generatedValue;
    }

    /**
     * Current
     *
     * @return array|bool|mixed
     */
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
    public function next()
    {
        $this->position++;
    }

    /**
     * Key
     *
     * @return int|mixed
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Valid
     *
     * @return bool
     */
    public function valid()
    {
        return ($this->position < $this->count);
    }

    /**
     * Rewind
     *
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }

    public function buffer(): void
    {
    }

    public function isBuffered(): bool
    {
        return false;
    }

    public function isQueryResult(): bool
    {
        return (pg_num_fields($this->resource) > 0);
    }

    public function getAffectedRows(): int
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

    public function count()
    {
        return $this->count;
    }

    public function getFieldCount(): int
    {
        return pg_num_fields($this->resource);
    }
}
