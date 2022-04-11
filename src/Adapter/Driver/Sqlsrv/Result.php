<?php

namespace Laminas\Db\Adapter\Driver\Sqlsrv;

use Iterator;
use Laminas\Db\Adapter\Driver\ResultInterface;
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use ReturnTypeWillChange;

use function is_bool;
use function sqlsrv_fetch_array;
use function sqlsrv_num_fields;
use function sqlsrv_num_rows;
use function sqlsrv_rows_affected;

use const SQLSRV_FETCH_ASSOC;
use const SQLSRV_SCROLL_FIRST;
use const SQLSRV_SCROLL_NEXT;

class Result implements Iterator, ResultInterface
{
    /** @var resource */
    protected $resource;

    /** @var bool */
    protected $currentData = false;

    /** @var bool */
    protected $currentComplete = false;

    /** @var int */
    protected $position = -1;

    /** @var mixed */
    protected $generatedValue;

    /**
     * Initialize
     *
     * @param  resource $resource
     * @param  mixed    $generatedValue
     * @return $this Provides a fluent interface
     */
    public function initialize($resource, $generatedValue = null)
    {
        $this->resource       = $resource;
        $this->generatedValue = $generatedValue;
        return $this;
    }

    /**
     * @return null
     */
    public function buffer()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isBuffered()
    {
        return false;
    }

    /**
     * Get resource
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Current
     *
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }

        $this->load();
        return $this->currentData;
    }

    /**
     * Next
     *
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        $this->load();
        return true;
    }

    /**
     * Load
     *
     * @param  int $row
     * @return mixed
     */
    protected function load($row = SQLSRV_SCROLL_NEXT)
    {
        $this->currentData     = sqlsrv_fetch_array($this->resource, SQLSRV_FETCH_ASSOC, $row);
        $this->currentComplete = true;
        $this->position++;
        return $this->currentData;
    }

    /**
     * Key
     *
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    /**
     * Rewind
     *
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
        $this->load(SQLSRV_SCROLL_FIRST);
        return true;
    }

    /**
     * Valid
     *
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function valid()
    {
        if ($this->currentComplete && $this->currentData) {
            return true;
        }

        return $this->load();
    }

    /**
     * Count
     *
     * @return int
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        return sqlsrv_num_rows($this->resource);
    }

    /**
     * @return bool|int
     */
    public function getFieldCount()
    {
        return sqlsrv_num_fields($this->resource);
    }

    /**
     * Is query result
     *
     * @return bool
     */
    public function isQueryResult()
    {
        if (is_bool($this->resource)) {
            return false;
        }
        return sqlsrv_num_fields($this->resource) > 0;
    }

    /**
     * Get affected rows
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return sqlsrv_rows_affected($this->resource);
    }

    /**
     * @return mixed|null
     */
    public function getGeneratedValue()
    {
        return $this->generatedValue;
    }
}
