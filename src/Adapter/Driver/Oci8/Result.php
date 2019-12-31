<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Driver\Oci8;

use Iterator;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Exception;

class Result implements Iterator, ResultInterface
{

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * @var bool
     */
    protected $isBuffered = null;

    /**
     * Cursor position
     * @var int
     */
    protected $position = 0;

    /**
     * Number of known rows
     * @var int
     */
    protected $numberOfRows = -1;

    /**
     * Is the current() operation already complete for this pointer position?
     * @var bool
     */
    protected $currentComplete = false;

    /**
     * @var bool
     */
    protected $currentData = false;

    /**
     *
     * @var array
     */
    protected $statementBindValues = array('keys' => null, 'values' => array());

    /**
     * @var mixed
     */
    protected $generatedValue = null;

    /**
     * Initialize
     * @param resource $resource
     * @return Result
     */
    public function initialize($resource /*, $generatedValue, $isBuffered = null*/)
    {
        if (!is_resource($resource) && get_resource_type($resource) !== 'oci8 statement') {
            throw new Exception\InvalidArgumentException('Invalid resource provided.');
        }
        $this->resource = $resource;
        return $this;
    }

    /**
     * Force buffering at driver level
     *
     * Oracle does not support this, to my knowledge (@ralphschindler)
     *
     * @throws Exception\RuntimeException
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
        return (oci_num_fields($this->resource) > 0);
    }

    /**
     * Get affected rows
     * @return int
     */
    public function getAffectedRows()
    {
        return oci_num_rows($this->resource);
    }

    /**
     * Current
     * @return mixed
     */
    public function current()
    {
        if ($this->currentComplete == false) {
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
        $this->currentData = oci_fetch_assoc($this->resource);

        if ($this->currentData !== false) {
            $this->position++;
            return true;
        }
        return false;
    }

    /**
     * Next
     */
    public function next()
    {
        return $this->loadData();
    }

    /**
     * Key
     * @return mixed
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Rewind
     */
    public function rewind()
    {
        if ($this->position > 0) {
            throw new Exception\RuntimeException('Oci8 results cannot be rewound for multiple iterations');
        }
    }

    /**
     * Valid
     * @return bool
     */
    public function valid()
    {
        if ($this->currentComplete) {
            return ($this->currentData !== false);
        }

        return $this->loadData();
    }

    /**
     * Count
     * @return int
     */
    public function count()
    {
        // @todo OCI8 row count in Driver Result
        return null;
    }

    /**
     * @return int
     */
    public function getFieldCount()
    {
        return oci_num_fields($this->resource);
    }

    /**
     * @return mixed|null
     */
    public function getGeneratedValue()
    {
        // @todo OCI8 generated value in Driver Result
        return null;
    }

}
