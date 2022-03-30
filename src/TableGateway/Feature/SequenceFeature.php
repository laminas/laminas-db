<?php

namespace Laminas\Db\TableGateway\Feature;

use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Sql\Insert;

use function array_search;

class SequenceFeature extends AbstractFeature
{
    /** @var string */
    protected $primaryKeyField;

    /** @var string */
    protected $sequenceName;

    /** @var int */
    protected $sequenceValue;

    /**
     * @param string $primaryKeyField
     * @param string $sequenceName
     */
    public function __construct($primaryKeyField, $sequenceName)
    {
        $this->primaryKeyField = $primaryKeyField;
        $this->sequenceName    = $sequenceName;
    }

    /**
     * @return Insert
     */
    public function preInsert(Insert $insert)
    {
        $columns = $insert->getRawState('columns');
        $values  = $insert->getRawState('values');
        $key     = array_search($this->primaryKeyField, $columns);
        if ($key !== false) {
            $this->sequenceValue = $values[$key] ?? null;
            return $insert;
        }

        $this->sequenceValue = $this->nextSequenceId();
        if ($this->sequenceValue === null) {
            return $insert;
        }

        $insert->values([$this->primaryKeyField => $this->sequenceValue], Insert::VALUES_MERGE);
        return $insert;
    }

    public function postInsert(StatementInterface $statement, ResultInterface $result)
    {
        if ($this->sequenceValue !== null) {
            $this->tableGateway->lastInsertValue = $this->sequenceValue;
        }
    }

    /**
     * Generate a new value from the specified sequence in the database, and return it.
     *
     * @return int
     */
    public function nextSequenceId()
    {
        $platform     = $this->tableGateway->adapter->getPlatform();
        $platformName = $platform->getName();

        switch ($platformName) {
            case 'Oracle':
                $sql = 'SELECT ' . $platform->quoteIdentifier($this->sequenceName) . '.NEXTVAL as "nextval" FROM dual';
                break;
            case 'PostgreSQL':
                $sql = 'SELECT NEXTVAL(\'"' . $this->sequenceName . '"\')';
                break;
            default:
                return;
        }

        $statement = $this->tableGateway->adapter->createStatement();
        $statement->prepare($sql);
        $result   = $statement->execute();
        $sequence = $result->current();
        unset($statement, $result);
        return $sequence['nextval'];
    }

    /**
     * Return the most recent value from the specified sequence in the database.
     *
     * @return int
     */
    public function lastSequenceId()
    {
        $platform     = $this->tableGateway->adapter->getPlatform();
        $platformName = $platform->getName();

        switch ($platformName) {
            case 'Oracle':
                $sql = 'SELECT ' . $platform->quoteIdentifier($this->sequenceName) . '.CURRVAL as "currval" FROM dual';
                break;
            case 'PostgreSQL':
                $sql = 'SELECT CURRVAL(\'' . $this->sequenceName . '\')';
                break;
            default:
                return;
        }

        $statement = $this->tableGateway->adapter->createStatement();
        $statement->prepare($sql);
        $result   = $statement->execute();
        $sequence = $result->current();
        unset($statement, $result);
        return $sequence['currval'];
    }
}
