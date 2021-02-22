<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\TestAsset;

use Laminas\Db\Adapter\AdapterAwareInterface;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\Db\Adapter\AdapterInterface;

class ConcreteAdapterAwareObject implements AdapterAwareInterface
{
    use AdapterAwareTrait;

    /** @var array */
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function getAdapter() : ?AdapterInterface
    {
        return $this->adapter;
    }

    public function getOptions() : array
    {
        return $this->options;
    }
}
