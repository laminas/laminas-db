<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Driver\Pdo;

use Iterator;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Exception;
use PDOStatement;

class Result implements Iterator, ResultInterface
{
    const STATEMENT_MODE_SCROLLABLE = 'scrollable';
    const STATEMENT_MODE_FORWARD    = 'forward';

    /**
     *
     * @var string
     */
    protected $statementMode = self::STATEMENT_MODE_FORWARD;

    /**
     * @var int
     */
    protected $fetchMode = \PDO::FETCH_ASSOC;

    /**
     * @var PDOStatement
     */
    protected $resource = null;

    /**
     * @var array Result options
     */
    protected $options;

    /**
     * Is the current complete?
     * @var bool
     */
    protected $currentComplete = false;

    /**
     * Track current item in recordset
     * @var mixed
     */
    protected $currentData = null;

    /**
     * Current position of scrollable statement
     * @var int
     */
    protected $position = -1;

    /**
     * @var mixed
     */
    protected $generatedValue = null;

    /**
     * @var null
     */
    protected $rowCount = 0;

    /**
     * Initialize
     *
     * @param  PDOStatement $resource
     * @param               $generatedValue
     * @return self Provides a fluent interface
     */
    public function initialize(PDOStatement $resource, $generatedValue)
    {
        $this->resource = $resource;
        $this->generatedValue = $generatedValue;

        return $this;
    }

    public function setRowCount(int $rowCount): void
    {
        $this->rowCount = $rowCount;
    }

    public function buffer(): void
    {
    }

    public function isBuffered(): bool
    {
        return false;
    }

    /**
     * @param int $fetchMode
     * @throws Exception\InvalidArgumentException on invalid fetch mode
     */
    public function setFetchMode($fetchMode)
    {
        if ($fetchMode < 1 || $fetchMode > 10) {
            throw new Exception\InvalidArgumentException(
                'The fetch mode must be one of the PDO::FETCH_* constants.'
            );
        }

        $this->fetchMode = (int) $fetchMode;
    }

    /**
     * @return int
     */
    public function getFetchMode()
    {
        return $this->fetchMode;
    }

    /**
     * Get resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get the data
     * @return array
     */
    public function current()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }

        $this->currentData = $this->resource->fetch($this->fetchMode);
        $this->currentComplete = true;
        return $this->currentData;
    }

    /**
     * Next
     *
     * @return mixed
     */
    public function next()
    {
        $this->currentData = $this->resource->fetch($this->fetchMode);
        $this->currentComplete = true;
        $this->position++;
        return $this->currentData;
    }

    /**
     * Key
     *
     * @return mixed
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @throws Exception\RuntimeException
     * @return void
     */
    public function rewind()
    {
        if ($this->statementMode == self::STATEMENT_MODE_FORWARD && $this->position > 0) {
            throw new Exception\RuntimeException(
                'This result is a forward only result set, calling rewind() after moving forward is not supported'
            );
        }
        $this->currentData = $this->resource->fetch($this->fetchMode);
        $this->currentComplete = true;
        $this->position = 0;
    }

    /**
     * Valid
     *
     * @return bool
     */
    public function valid()
    {
        return ($this->currentData !== false);
    }

    public function count(): int
    {
        if (is_int($this->rowCount)) {
            return $this->rowCount;
        }
        if ($this->rowCount instanceof \Closure) {
            $this->rowCount = (int) call_user_func($this->rowCount);
        } else {
            $this->rowCount = (int) $this->resource->rowCount();
        }
        return $this->rowCount;
    }

    public function getFieldCount(): int
    {
        return $this->resource->columnCount();
    }

    public function isQueryResult(): bool
    {
        return ($this->resource->columnCount() > 0);
    }

    public function getAffectedRows(): int
    {
        return $this->resource->rowCount();
    }

    /**
     * @return mixed|null
     */
    public function getGeneratedValue()
    {
        return $this->generatedValue;
    }
}
