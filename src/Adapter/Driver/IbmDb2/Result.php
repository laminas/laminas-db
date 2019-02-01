<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver\IbmDb2;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Exception;

class Result implements ResultInterface
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var bool
     */
    protected $currentComplete = false;

    /**
     * @var mixed
     */
    protected $currentData = null;

    /**
     * @var mixed
     */
    protected $generatedValue = null;

    /**
     * @param  resource $resource
     * @param  mixed $generatedValue
     * @return self Provides a fluent interface
     */
    public function initialize($resource, $generatedValue = null)
    {
        $this->resource = $resource;
        $this->generatedValue = $generatedValue;
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }

        $this->currentData = db2_fetch_assoc($this->resource);
        return $this->currentData;
    }

    /**
     * @return mixed
     */
    public function next()
    {
        $this->currentData = db2_fetch_assoc($this->resource);
        $this->currentComplete = true;
        $this->position++;
        return $this->currentData;
    }

    /**
     * @return int|string
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return ($this->currentData !== false);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        if ($this->position > 0) {
            throw new Exception\RuntimeException(
                'This result is a forward only result set, calling rewind() after moving forward is not supported'
            );
        }
        $this->currentData = db2_fetch_assoc($this->resource);
        $this->currentComplete = true;
        $this->position = 1;
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
        return (db2_num_fields($this->resource) > 0);
    }

    public function getAffectedRows(): int
    {
        return db2_num_rows($this->resource);
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
     * Get the resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    public function getFieldCount(): int
    {
        return db2_num_fields($this->resource);
    }

    /**
     * @return null|int
     */
    public function count()
    {
        return;
    }
}
