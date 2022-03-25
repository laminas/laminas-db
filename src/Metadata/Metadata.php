<?php

declare(strict_types=1);

namespace Laminas\Db\Metadata;

use Laminas\Db\Adapter\Adapter;

/**
 * @deprecated Use Laminas\Db\Metadata\Source\Factory::createSourceFromAdapter($adapter)
 */
class Metadata implements MetadataInterface
{
    /** @var MetadataInterface */
    protected $source;

    /**
     * Constructor
     */
    public function __construct(Adapter $adapter)
    {
        $this->source = Source\Factory::createSourceFromAdapter($adapter);
    }

    /**
     * {@inheritdoc}
     */
    public function getTables($schema = null, $includeViews = false)
    {
        return $this->source->getTables($schema, $includeViews);
    }

    /**
     * {@inheritdoc}
     */
    public function getViews($schema = null)
    {
        return $this->source->getViews($schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getTriggers($schema = null)
    {
        return $this->source->getTriggers($schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints($table, $schema = null)
    {
        return $this->source->getConstraints($table, $schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns($table, $schema = null)
    {
        return $this->source->getColumns($table, $schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintKeys($constraint, $table, $schema = null)
    {
        return $this->source->getConstraintKeys($constraint, $table, $schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint($constraintName, $table, $schema = null)
    {
        return $this->source->getConstraint($constraintName, $table, $schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemas()
    {
        return $this->source->getSchemas();
    }

    /**
     * {@inheritdoc}
     */
    public function getTableNames($schema = null, $includeViews = false)
    {
        return $this->source->getTableNames($schema, $includeViews);
    }

    /**
     * {@inheritdoc}
     */
    public function getTable($tableName, $schema = null)
    {
        return $this->source->getTable($tableName, $schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewNames($schema = null)
    {
        return $this->source->getViewNames($schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getView($viewName, $schema = null)
    {
        return $this->source->getView($viewName, $schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getTriggerNames($schema = null)
    {
        return $this->source->getTriggerNames($schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getTrigger($triggerName, $schema = null)
    {
        return $this->source->getTrigger($triggerName, $schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnNames($table, $schema = null)
    {
        return $this->source->getColumnNames($table, $schema);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn($columnName, $table, $schema = null)
    {
        return $this->source->getColumn($columnName, $table, $schema);
    }
}
