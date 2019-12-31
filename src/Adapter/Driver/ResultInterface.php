<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Driver;

use Countable;
use Iterator;
use resource;

interface ResultInterface extends
    Countable,
    Iterator
{
    /**
     * Force buffering
     */
    public function buffer(): void;

    public function isBuffered(): bool;

    public function isQueryResult(): bool;

    public function getAffectedRows(): int;

    /**
     * Get generated value
     *
     * @return mixed|null
     */
    public function getGeneratedValue();

    /**
     * @return mixed
     */
    public function getResource();

    public function getFieldCount(): int;
}
