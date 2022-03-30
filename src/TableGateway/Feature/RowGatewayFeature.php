<?php

namespace Laminas\Db\TableGateway\Feature;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\RowGateway\RowGateway;
use Laminas\Db\RowGateway\RowGatewayInterface;
use Laminas\Db\TableGateway\Exception;
use Laminas\Db\TableGateway\Feature\MetadataFeature;

use function func_get_args;
use function is_string;

class RowGatewayFeature extends AbstractFeature
{
    /** @var array */
    protected $constructorArguments = [];

    public function __construct()
    {
        $this->constructorArguments = func_get_args();
    }

    public function postInitialize()
    {
        $args = $this->constructorArguments;

        /** @var ResultSet $resultSetPrototype */
        $resultSetPrototype = $this->tableGateway->resultSetPrototype;

        if (! $this->tableGateway->resultSetPrototype instanceof ResultSet) {
            throw new Exception\RuntimeException(
                'This feature ' . self::class . ' expects the ResultSet to be an instance of ' . ResultSet::class
            );
        }

        if (isset($args[0])) {
            if (is_string($args[0])) {
                $primaryKey          = $args[0];
                $rowGatewayPrototype = new RowGateway(
                    $primaryKey,
                    $this->tableGateway->table,
                    $this->tableGateway->adapter
                );
                $resultSetPrototype->setArrayObjectPrototype($rowGatewayPrototype);
            } elseif ($args[0] instanceof RowGatewayInterface) {
                $rowGatewayPrototype = $args[0];
                $resultSetPrototype->setArrayObjectPrototype($rowGatewayPrototype);
            }
        } else {
            // get from metadata feature
            $metadata = $this->tableGateway->featureSet->getFeatureByClassName(
                MetadataFeature::class
            );
            if ($metadata === false || ! isset($metadata->sharedData['metadata'])) {
                throw new Exception\RuntimeException(
                    'No information was provided to the RowGatewayFeature and/or no MetadataFeature could be consulted '
                    . 'to find the primary key necessary for RowGateway object creation.'
                );
            }
            $primaryKey          = $metadata->sharedData['metadata']['primaryKey'];
            $rowGatewayPrototype = new RowGateway(
                $primaryKey,
                $this->tableGateway->table,
                $this->tableGateway->adapter
            );
            $resultSetPrototype->setArrayObjectPrototype($rowGatewayPrototype);
        }
    }
}
