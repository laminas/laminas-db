<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Predicate;

use Laminas\Db\Sql\Exception;
use Laminas\Db\Sql\Select;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Sql
 */
class In implements PredicateInterface
{
    protected $identifier;
    protected $valueSet;

    /**
     * Constructor
     *
     * @param  null|string $identifier
     * @param  array $valueSet
     */
    public function __construct($identifier = null, $valueSet = null)
    {
        if ($identifier) {
            $this->setIdentifier($identifier);
        }
        if ($valueSet) {
            $this->setValueSet($valueSet);
        }
    }

    /**
     * Set identifier for comparison
     *
     * @param  string $identifier
     * @return In
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * Get identifier of comparison
     *
     * @return null|string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set set of values for IN comparison
     *
     * @param  array $valueSet
     * @return In
     */
    public function setValueSet($valueSet)
    {
        if (!is_array($valueSet) && !$valueSet instanceof Select) {
            throw new Exception\InvalidArgumentException(
                '$valueSet must be either an array or a Laminas\Db\Sql\Select object, ' . gettype($valueSet) . ' given'
            );
        }
        $this->valueSet = $valueSet;
        return $this;
    }

    public function getValueSet()
    {
        return $this->valueSet;
    }

    /**
     * Return array of parts for where statement
     *
     * @return array
     */
    public function getExpressionData()
    {
        $values = $this->getValueSet();
        if ($values instanceof Select) {
            $specification = '%s IN %s';
            $types = array(self::TYPE_SELECT);
            $values = array($values);
        } else {
            $specification = '%s IN (' . implode(', ', array_fill(0, count($values), '%s')) . ')';
            $types = array_fill(0, count($values), self::TYPE_VALUE);
        }

        $identifier = $this->getIdentifier();
        array_unshift($values, $identifier);
        array_unshift($types, self::TYPE_IDENTIFIER);

        return array(array(
            $specification,
            $values,
            $types,
        ));
    }

}
