<?php

namespace Laminas\Db\Adapter\Driver\Oci8;

use Iterator;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Exception;
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use ReturnTypeWillChange;

use function call_user_func;
use function get_resource_type;
use function is_callable;
use function is_int;
use function is_resource;

class Result implements Iterator, ResultInterface
{
    /** @var resource */
    protected $resource;

    /** @var null|int */
    protected $rowCount;

    /**
     * Cursor position
     *
     * @var int
     */
    protected $position = 0;

    /**
     * Number of known rows
     *
     * @var int
     */
    protected $numberOfRows = -1;

    /**
     * Is the current() operation already complete for this pointer position?
     *
     * @var bool
     */
    protected $currentComplete = false;

    /** @var bool|array */
    protected $currentData = false;

    /** @var array */
    protected $statementBindValues = ['keys' => null, 'values' => []];

    /** @var mixed */
    protected $generatedValue;

    /**
     * Initialize
     *
     * @param resource $resource
     * @param null|int $generatedValue
     * @param null|int $rowCount
     * @return self Provides a fluent interface
     */
    public function initialize($resource, $generatedValue = null, $rowCount = null)
    {
        if (! is_resource($resource) && get_resource_type($resource) !== 'oci8 statement') {
            throw new Exception\InvalidArgumentException('Invalid resource provided.');
        }
        $this->resource       = $resource;
        $this->generatedValue = $generatedValue;
        $this->rowCount       = $rowCount;
        return $this;
    }

    /**
     * Force buffering at driver level
     *
     * Oracle does not support this, to my knowledge (@ralphschindler)
     *
     * @return null
     */
    public function buffer()
    {
        return null;
    }

    /**
     * Is the result buffered?
     *
     * @return bool
     */
    public function isBuffered()
    {
        return false;
    }

    /**
     * Return the resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Is query result?
     *
     * @return bool
     */
    public function isQueryResult()
    {
        return oci_num_fields($this->resource) > 0;
    }

    /**
     * Get affected rows
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return oci_num_rows($this->resource);
    }

    /** @return mixed */
    #[ReturnTypeWillChange]
    public function current()
    {
        if ($this->currentComplete === false) {
            if ($this->loadData() === false) {
                return false;
            }
        }
        return $this->currentData;
    }

    /**
     * Load from oci8 result
     *
     * @return bool
     */
    protected function loadData()
    {
        $this->currentComplete = true;
        $this->currentData     = oci_fetch_assoc($this->resource);
        if ($this->currentData !== false) {
            $this->position++;
            return true;
        }
        return false;
    }

    /** @return void */
    #[ReturnTypeWillChange]
    public function next()
    {
        $this->loadData();
    }

    /** @return int|string */
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    /** @return void */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        if ($this->position > 0) {
            throw new Exception\RuntimeException('Oci8 results cannot be rewound for multiple iterations');
        }
    }

    /** @return bool */
    #[ReturnTypeWillChange]
    public function valid()
    {
        if ($this->currentComplete) {
            return $this->currentData !== false;
        }
        return $this->loadData();
    }

    /** @return int */
    #[ReturnTypeWillChange]
    public function count()
    {
        if (is_int($this->rowCount)) {
            return $this->rowCount;
        }

        if (is_callable($this->rowCount)) {
            $this->rowCount = (int) call_user_func($this->rowCount);
            return $this->rowCount;
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getFieldCount()
    {
        return oci_num_fields($this->resource);
    }

    /**
     * @todo OCI8 generated value in Driver Result
     * @return null
     */
    public function getGeneratedValue()
    {
        return null;
    }
}
