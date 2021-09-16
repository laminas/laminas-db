<?php

namespace Laminas\Db\RowGateway\Feature;

use Laminas\Db\RowGateway\AbstractRowGateway;

use function call_user_func_array;
use function method_exists;

class FeatureSet
{
    public const APPLY_HALT = 'halt';

    /** @var AbstractRowGateway */
    protected $rowGateway;

    /** @var AbstractFeature[] */
    protected $features = [];

    /** @var array */
    protected $magicSpecifications = [];

    /**
     * @param array $features
     */
    public function __construct(array $features = [])
    {
        if ($features) {
            $this->addFeatures($features);
        }
    }

    /**
     * @return self Provides a fluent interface
     */
    public function setRowGateway(AbstractRowGateway $rowGateway)
    {
        $this->rowGateway = $rowGateway;
        foreach ($this->features as $feature) {
            $feature->setRowGateway($this->rowGateway);
        }
        return $this;
    }

    /**
     * @param string $featureClassName
     * @return AbstractFeature
     */
    public function getFeatureByClassName($featureClassName)
    {
        $feature = false;
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
        $this->features[] = $feature;
        $feature->setRowGateway($feature);
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
     * @param string $method
     * @return bool
     */
    public function canCallMagicCall($method)
    {
        return false;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function callMagicCall($method, $arguments)
    {
        return null;
    }
}
