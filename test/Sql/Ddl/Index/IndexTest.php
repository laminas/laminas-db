<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Index;

use Laminas\Db\Sql\Ddl\Index\Index;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Sql\Ddl\Index\Index::getExpressionData
     */
    public function testGetExpressionData()
    {
        $uk = new Index('foo', 'my_uk');
        $this->assertEquals(
            array(array(
                'INDEX %s(%s)',
                array('my_uk', 'foo'),
                array($uk::TYPE_IDENTIFIER, $uk::TYPE_IDENTIFIER)
            )),
            $uk->getExpressionData()
        );
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Index\Index::getExpressionData
     */
    public function testGetExpressionDataWithLength()
    {
        $key = new Index(array('foo', 'bar'), 'my_uk', array(10, 5));
        $this->assertEquals(
            array(array(
                'INDEX %s(%s(10), %s(5))',
                array('my_uk', 'foo', 'bar'),
                array($key::TYPE_IDENTIFIER, $key::TYPE_IDENTIFIER, $key::TYPE_IDENTIFIER)
            )),
            $key->getExpressionData()
        );
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Index\Index::getExpressionData
     */
    public function testGetExpressionDataWithLengthUnmatched()
    {
        $key = new Index(array('foo', 'bar'), 'my_uk', array(10));
        $this->assertEquals(
            array(array(
                'INDEX %s(%s(10), %s)',
                array('my_uk', 'foo', 'bar'),
                array($key::TYPE_IDENTIFIER, $key::TYPE_IDENTIFIER, $key::TYPE_IDENTIFIER)
            )),
            $key->getExpressionData()
        );
    }
}
