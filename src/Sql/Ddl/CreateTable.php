<?php

namespace Laminas\Db\Sql\Ddl;

use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Sql\AbstractSql;
use Laminas\Db\Sql\TableIdentifier;

use function array_key_exists;

class CreateTable extends AbstractSql implements SqlInterface
{
    public const COLUMNS     = 'columns';
    public const CONSTRAINTS = 'constraints';
    public const TABLE       = 'table';

    /** @var Column\ColumnInterface[] */
    protected $columns = [];

    /** @var string[] */
    protected $constraints = [];

    /** @var bool */
    protected $isTemporary = false;

    /**
     * {@inheritDoc}
     */
    protected $specifications = [
        self::TABLE       => 'CREATE %1$sTABLE %2$s (',
        self::COLUMNS     => [
            "\n    %1\$s" => [
                [1 => '%1$s', 'combinedby' => ",\n    "],
            ],
        ],
        'combinedBy'      => ",",
        self::CONSTRAINTS => [
            "\n    %1\$s" => [
                [1 => '%1$s', 'combinedby' => ",\n    "],
            ],
        ],
        'statementEnd'    => '%1$s',
    ];

    /** @var string */
    protected $table = '';

    /**
     * @param string|TableIdentifier $table
     * @param bool   $isTemporary
     */
    public function __construct($table = '', $isTemporary = false)
    {
        $this->table = $table;
        $this->setTemporary($isTemporary);
    }

    /**
     * @param  bool $temporary
     * @return $this Provides a fluent interface
     */
    public function setTemporary($temporary)
    {
        $this->isTemporary = (bool) $temporary;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTemporary()
    {
        return $this->isTemporary;
    }

    /**
     * @param  string $name
     * @return $this Provides a fluent interface
     */
    public function setTable($name)
    {
        $this->table = $name;
        return $this;
    }

    /**
     * @return $this Provides a fluent interface
     */
    public function addColumn(Column\ColumnInterface $column)
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @return $this Provides a fluent interface
     */
    public function addConstraint(Constraint\ConstraintInterface $constraint)
    {
        $this->constraints[] = $constraint;
        return $this;
    }

    /**
     * @param  string|null $key
     * @return array
     */
    public function getRawState($key = null)
    {
        $rawState = [
            self::COLUMNS     => $this->columns,
            self::CONSTRAINTS => $this->constraints,
            self::TABLE       => $this->table,
        ];

        return isset($key) && array_key_exists($key, $rawState) ? $rawState[$key] : $rawState;
    }

    /**
     * @return string[]
     */
    protected function processTable(?PlatformInterface $adapterPlatform = null)
    {
        return [
            $this->isTemporary ? 'TEMPORARY ' : '',
            $this->resolveTable($this->table, $adapterPlatform),
        ];
    }

    /**
     * @return string[][]|null
     */
    protected function processColumns(?PlatformInterface $adapterPlatform = null)
    {
        if (! $this->columns) {
            return;
        }

        $sqls = [];

        foreach ($this->columns as $column) {
            $sqls[] = $this->processExpression($column, $adapterPlatform);
        }

        return [$sqls];
    }

    /**
     * @return array|string
     */
    protected function processCombinedby(?PlatformInterface $adapterPlatform = null)
    {
        if ($this->constraints && $this->columns) {
            return $this->specifications['combinedBy'];
        }
    }

    /**
     * @return string[][]|null
     */
    protected function processConstraints(?PlatformInterface $adapterPlatform = null)
    {
        if (! $this->constraints) {
            return;
        }

        $sqls = [];

        foreach ($this->constraints as $constraint) {
            $sqls[] = $this->processExpression($constraint, $adapterPlatform);
        }

        return [$sqls];
    }

    /**
     * @return string[]
     */
    protected function processStatementEnd(?PlatformInterface $adapterPlatform = null)
    {
        return ["\n)"];
    }
}
