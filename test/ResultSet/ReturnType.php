<?php
namespace ZendTest\Db\ResultSet;

/**
 * This class is just here to be able to provide the simplest possible custom return type
 * for a ResultSet when calling ResultSet::setArrayObjectPrototype or using the constructor argument.
 *
 * Class ReturnType
 * @package ZendTest\Db\ResultSet
 */
class ReturnType
{
    public function exchangeArray(array $values)
    {
    }
}
