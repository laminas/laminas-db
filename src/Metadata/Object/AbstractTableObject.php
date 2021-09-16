<?php

namespace Laminas\Db\Metadata\Object;

abstract class AbstractTableObject
{
    /*
    protected $catalogName = null;
    protected $schemaName = null;
    */

    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var array */
    protected $columns;

    /** @var array */
    protected $constraints;

    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct($name)
    {
        if ($name) {
            $this->setName($name);
        }
    }

    /**
     * Set columns
     *
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * Get columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set constraints
     *
     * @param array $constraints
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
    }

    /**
     * Get constraints
     *
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
