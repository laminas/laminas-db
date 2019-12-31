<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\TableGateway\Feature;

use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Sql\Insert;

class SequenceFeature extends AbstractFeature
{
    /**
     * @var string
     */
    protected $primaryKeyField;

    /**
     * @var string
     */
    protected $sequenceName;

    /**
     * @var int
     */
    protected $sequenceValue;


    /**
     * @param null $sequence
     */
    public function __construct($primaryKeyField, $sequenceName)
    {
        $this->primaryKeyField = $primaryKeyField;
        $this->sequenceName    = $sequenceName;
    }


    public function preInsert(Insert $insert)
    {
        $columns = $insert->getRawState('columns');
        $values = $insert->getRawState('values');
        $key = array_search($this->primaryKeyField, $columns);
        if ($key !== false) {
            $this->sequenceValue = $values[$key];
            return $insert;
        }

        $this->sequenceValue = $this->nextSequenceId();
        if ($this->sequenceValue === null)
            return $insert;

        array_push($columns, $this->primaryKeyField);
        array_push($values, $this->sequenceValue);
        $insert->columns($columns);
        $insert->values($values);
        return $insert;
    }

    public function postInsert(StatementInterface $statement, ResultInterface $result)
    {
        if ($this->sequenceValue !== null)
            $this->tableGateway->lastInsertValue = $this->sequenceValue;
    }

    /**
     * Generate a new value from the specified sequence in the database, and return it.
     * @return int
     */
    public function nextSequenceId()
    {
        $platform = $this->tableGateway->adapter->getPlatform();
        $platformName = $platform->getName();

        $sql = '';
        switch ($platformName) {
            case 'Oracle':
                $sql = 'SELECT ' . $platform->quoteIdentifier($this->sequenceName) . '.NEXTVAL FROM dual';
                break;
            case 'PostgreSQL':
                $sql = 'SELECT NEXTVAL(\'' . $this->sequenceName . '\')';
                break;
            default :
                return null;
        }

        $statement = $this->tableGateway->adapter->createStatement();
        $statement->prepare($sql);
        $result = $statement->execute();
        $sequence = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
        unset($statement, $result);
        return $sequence['nextval'];
    }

    /**
     * Return the most recent value from the specified sequence in the database.
     * @return int
     */
    public function lastSequenceId()
    {
        $platform = $this->tableGateway->adapter->getPlatform();
        $platformName = $platform->getName();

        $sql = '';
        switch ($platformName) {
            case 'Oracle':
                $sql = 'SELECT ' . $platform->quoteIdentifier($this->sequenceName) . '.CURRVAL FROM dual';
                break;
            case 'PostgreSQL':
                $sql = 'SELECT CURRVAL(\'' . $this->sequenceName . '\')';
                break;
            default :
                return null;
        }

        $statement = $this->tableGateway->adapter->createStatement();
        $statement->prepare($sql);
        $result = $statement->execute();
        $sequence = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
        unset($statement, $result);
        return $sequence['currval'];
    }
}
