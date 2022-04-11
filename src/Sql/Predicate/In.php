<?php

namespace Laminas\Db\Sql\Predicate;

use Laminas\Db\Sql\AbstractExpression;
use Laminas\Db\Sql\Exception;
use Laminas\Db\Sql\Select;

use function array_fill;
use function count;
use function gettype;
use function implode;
use function is_array;
use function vsprintf;

class In extends AbstractExpression implements PredicateInterface
{
    /** @var null|string|array */
    protected $identifier;

    /** @var null|array|Select */
    protected $valueSet;

    /** @var string */
    protected $specification = '%s IN %s';

    /** @var string */
    protected $valueSpecSpecification = '%%s IN (%s)';

    /**
     * Constructor
     *
     * @param null|string|array $identifier
     * @param null|array|Select $valueSet
     */
    public function __construct($identifier = null, $valueSet = null)
    {
        if ($identifier) {
            $this->setIdentifier($identifier);
        }
        if ($valueSet !== null) {
            $this->setValueSet($valueSet);
        }
    }

    /**
     * Set identifier for comparison
     *
     * @param  string|array $identifier
     * @return $this Provides a fluent interface
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier of comparison
     *
     * @return null|string|array
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set set of values for IN comparison
     *
     * @param  array|Select                       $valueSet
     * @return $this Provides a fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function setValueSet($valueSet)
    {
        if (! is_array($valueSet) && ! $valueSet instanceof Select) {
            throw new Exception\InvalidArgumentException(
                '$valueSet must be either an array or a Laminas\Db\Sql\Select object, ' . gettype($valueSet) . ' given'
            );
        }
        $this->valueSet = $valueSet;

        return $this;
    }

    /**
     * Gets set of values in IN comparison
     *
     * @return array|Select
     */
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
        $identifier   = $this->getIdentifier();
        $values       = $this->getValueSet();
        $replacements = [];

        if (is_array($identifier)) {
            $countIdentifier        = count($identifier);
            $identifierSpecFragment = '(' . implode(', ', array_fill(0, $countIdentifier, '%s')) . ')';
            $types                  = array_fill(0, $countIdentifier, self::TYPE_IDENTIFIER);
            $replacements           = $identifier;
        } else {
            $identifierSpecFragment = '%s';
            $replacements[]         = $identifier;
            $types                  = [self::TYPE_IDENTIFIER];
        }

        if ($values instanceof Select) {
            $specification  = vsprintf(
                $this->specification,
                [$identifierSpecFragment, '%s']
            );
            $replacements[] = $values;
            $types[]        = self::TYPE_VALUE;
        } else {
            foreach ($values as $argument) {
                [$replacements[], $types[]] = $this->normalizeArgument($argument, self::TYPE_VALUE);
            }
            $countValues       = count($values);
            $valuePlaceholders = $countValues > 0 ? array_fill(0, $countValues, '%s') : [];
            $inValueList       = implode(', ', $valuePlaceholders);
            if ('' === $inValueList) {
                $inValueList = 'NULL';
            }
            $specification = vsprintf(
                $this->specification,
                [$identifierSpecFragment, '(' . $inValueList . ')']
            );
        }

        return [
            [
                $specification,
                $replacements,
                $types,
            ],
        ];
    }
}
