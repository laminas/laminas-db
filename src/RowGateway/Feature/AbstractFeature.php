<?php

namespace Laminas\Db\RowGateway\Feature;

use Laminas\Db\RowGateway\AbstractRowGateway;
use Laminas\Db\RowGateway\Exception;

abstract class AbstractFeature extends AbstractRowGateway
{
    /**
     * @var AbstractRowGateway
     */
    protected $rowGateway = null;

    /**
     * @var array
     */
    protected $sharedData = [];

    /**
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }

    /**
     * @param AbstractRowGateway $rowGateway
     */
    public function setRowGateway(AbstractRowGateway $rowGateway)
    {
        $this->rowGateway = $rowGateway;
    }

    /**
     * @throws \Laminas\Db\RowGateway\Exception\RuntimeException
     */
    public function initialize()
    {
        throw new Exception\RuntimeException('This method is not intended to be called on this object.');
    }

    /**
     * @return array
     */
    public function getMagicMethodSpecifications()
    {
        return [];
    }
}
