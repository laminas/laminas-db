<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver;

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
