<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\TableGateway;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage TableGateway
 */
class TableGateway extends AbstractTableGateway
{

    /**
     * Constructor
     *
     * @param string $table
     * @param Adapter $adapter
     * @param Feature\AbstractFeature|Feature\FeatureSet|Feature\AbstractFeature[] $features
     * @param ResultSetInterface $resultSetPrototype
     * @param Sql $sql
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($table, Adapter $adapter, $features = null, ResultSetInterface $resultSetPrototype = null, Sql $sql = null)
    {
        // table
        if (!(is_string($table) || $table instanceof TableIdentifier)) {
            throw new Exception\InvalidArgumentException('Table name must be a string or an instance of Laminas\Db\Sql\TableIdentifier');
        }
        $this->table = $table;

        // adapter
        $this->adapter = $adapter;

        // process features
        if ($features !== null) {
            if ($features instanceof Feature\AbstractFeature) {
                $features = array($features);
            }
            if (is_array($features)) {
                $this->featureSet = new Feature\FeatureSet($features);
            } elseif ($features instanceof Feature\FeatureSet) {
                $this->featureSet = $features;
            } else {
                throw new Exception\InvalidArgumentException(
                    'TableGateway expects $feature to be an instance of an AbstractFeature or a FeatureSet, or an array of AbstractFeatures'
                );
            }
        } else {
            $this->featureSet = new Feature\FeatureSet();
        }

        // result prototype
        $this->resultSetPrototype = ($resultSetPrototype) ?: new ResultSet;

        // Sql object (factory for select, insert, update, delete)
        $this->sql = ($sql) ?: new Sql($this->adapter, $this->table);

        // check sql object bound to same table
        if ($this->sql->getTable() != $this->table) {
            throw new Exception\InvalidArgumentException('The table inside the provided Sql object must match the table of this TableGateway');
        }

        $this->initialize();
    }
}
