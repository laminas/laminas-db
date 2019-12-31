<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Constraint;

use Laminas\Db\Sql\Ddl\Constraint\PrimaryKey;

class PrimaryKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Sql\Ddl\Constraint\PrimaryKey::getExpressionData
     */
    public function testGetExpressionData()
    {
        $pk = new PrimaryKey('foo');
        $this->assertEquals(
            array(array(
                'PRIMARY KEY (%s)',
                array('foo'),
                array($pk::TYPE_IDENTIFIER)
            )),
            $pk->getExpressionData()
        );
    }
}
