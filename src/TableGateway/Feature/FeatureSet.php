<?php

namespace Laminas\Db\TableGateway\Feature;

use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\TableGatewayInterface;

use function call_user_func_array;
use function method_exists;

class FeatureSet
{
    public const APPLY_HALT = 'halt';

    /** @var null|AbstractTableGateway */
    protected $tableGateway;

    /** @var AbstractFeature[] */
    protected $features = [];

    /** @var array */
    protected $magicSpecifications = [];

    public function __construct(array $features = [])
    {
        if ($features) {
            $this->addFeatures($features);
        }
    }

    /**
     * @return self Provides a fluent interface
     */
    public function setTableGateway(AbstractTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        foreach ($this->features as $feature) {
            $feature->setTableGateway($this->tableGateway);
        }
        return $this;
    }

    /**
     * @param string $featureClassName
     * @return null|AbstractFeature
     */
    public function getFeatureByClassName($featureClassName)
    {
        $feature = null;
        foreach ($this->features as $potentialFeature) {
            if ($potentialFeature instanceof $featureClassName) {
                $feature = $potentialFeature;
                break;
            }
        }
        return $feature;
    }

    /**
     * @param array $features
     * @return self Provides a fluent interface
     */
    public function addFeatures(array $features)
    {
        foreach ($features as $feature) {
            $this->addFeature($feature);
        }
        return $this;
    }

    /**
     * @return self Provides a fluent interface
     */
    public function addFeature(AbstractFeature $feature)
    {
        if ($this->tableGateway instanceof TableGatewayInterface) {
            $feature->setTableGateway($this->tableGateway);
        }
        $this->features[] = $feature;
        return $this;
    }

    /**
     * @param string $method
     * @param array $args
     * @return void
     */
    public function apply($method, $args)
    {
        foreach ($this->features as $feature) {
            if (method_exists($feature, $method)) {
                $return = call_user_func_array([$feature, $method], $args);
                if ($return === self::APPLY_HALT) {
                    break;
                }
            }
        }
    }

    /**
     * @param string $property
     * @return bool
     */
    public function canCallMagicGet($property)
    {
        return false;
    }

    /**
     * @param string $property
     * @return mixed
     */
    public function callMagicGet($property)
    {
        return null;
    }

    /**
     * @param string $property
     * @return bool
     */
    public function canCallMagicSet($property)
    {
        return false;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    public function callMagicSet($property, $value)
    {
        return null;
    }

    /**
     * Is the method requested available in one of the added features
     *
     * @param string $method
     * @return bool
     */
    public function canCallMagicCall($method)
    {
        if (! empty($this->features)) {
            foreach ($this->features as $feature) {
                if (method_exists($feature, $method)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Call method of on added feature as though it were a local method
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function callMagicCall($method, $arguments)
    {
        foreach ($this->features as $feature) {
            if (method_exists($feature, $method)) {
                return $feature->$method($arguments);
            }
        }

        return null;
    }
}
