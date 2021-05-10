<?php

namespace Laminas\Db\Metadata\Object;

class ViewObject extends AbstractTableObject
{
    protected $viewDefinition;
    protected $checkOption;
    protected $isUpdatable;

    /**
     * @return string $viewDefinition
     */
    public function getViewDefinition()
    {
        return $this->viewDefinition;
    }

    /**
     * @param string $viewDefinition to set
     * @return self Provides a fluent interface
     */
    public function setViewDefinition($viewDefinition)
    {
        $this->viewDefinition = $viewDefinition;
        return $this;
    }

    /**
     * @return string $checkOption
     */
    public function getCheckOption()
    {
        return $this->checkOption;
    }

    /**
     * @param string $checkOption to set
     * @return self Provides a fluent interface
     */
    public function setCheckOption($checkOption)
    {
        $this->checkOption = $checkOption;
        return $this;
    }

    /**
     * @return bool $isUpdatable
     */
    public function getIsUpdatable()
    {
        return $this->isUpdatable;
    }

    /**
     * @param bool $isUpdatable to set
     * @return self Provides a fluent interface
     */
    public function setIsUpdatable($isUpdatable)
    {
        $this->isUpdatable = $isUpdatable;
        return $this;
    }

    public function isUpdatable()
    {
        return $this->isUpdatable;
    }
}
