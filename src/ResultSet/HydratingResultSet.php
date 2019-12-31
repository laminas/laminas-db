<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\ResultSet;

use ArrayObject;
use Laminas\Stdlib\Hydrator\ArraySerializable;
use Laminas\Stdlib\Hydrator\HydratorInterface;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage ResultSet
 */
class HydratingResultSet extends AbstractResultSet
{
    /**
     * @var HydratorInterface
     */
    protected $hydrator = null;

    /**
     * @var null
     */
    protected $objectPrototype = null;

    /**
     * Constructor
     *
     * @param  null|HydratorInterface $hydrator
     * @param  null|object $objectPrototype
     */
    public function __construct(HydratorInterface $hydrator = null, $objectPrototype = null)
    {
        $this->setHydrator(($hydrator) ?: new ArraySerializable);
        $this->setObjectPrototype(($objectPrototype) ?: new ArrayObject);
    }

    /**
     * Set the row object prototype
     *
     * @param  object $objectPrototype
     * @throws Exception\InvalidArgumentException
     * @return ResultSet
     */
    public function setObjectPrototype($objectPrototype)
    {
        if (!is_object($objectPrototype)) {
            throw new Exception\InvalidArgumentException(
                'An object must be set as the object prototype, a ' . gettype($objectPrototype) . ' was provided.'
            );
        }
        $this->objectPrototype = $objectPrototype;
        return $this;
    }

    /**
     * Set the hydrator to use for each row object
     *
     * @param HydratorInterface $hydrator
     * @return HydratingResultSet
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * Get the hydrator to use for each row object
     *
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }

    /**
     * Iterator: get current item
     *
     * @return object
     */
    public function current()
    {
        $data = $this->dataSource->current();
        $object = clone $this->objectPrototype;
        return is_array($data) ? $this->hydrator->hydrate($data, $object) : false;
    }

    /**
     * Cast result set to array of arrays
     *
     * @return array
     * @throws Exception\RuntimeException if any row is not castable to an array
     */
    public function toArray()
    {
        $return = array();
        foreach ($this as $row) {
            $return[] = $this->getHydrator()->extract($row);
        }
        return $return;
    }
}
