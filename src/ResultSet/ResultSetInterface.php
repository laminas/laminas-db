<?php

namespace Laminas\Db\ResultSet;

use Countable;
use Traversable;

interface ResultSetInterface extends Traversable, Countable
{
    /**
     * Can be anything traversable|array
     *
     * @abstract
     * @param iterable $dataSource
     * @return mixed
     */
    public function initialize($dataSource);

    /**
     * Field terminology is more correct as information coming back
     * from the database might be a column, and/or the result of an
     * operation or intersection of some data
     *
     * @abstract
     * @return mixed
     */
    public function getFieldCount();
}
