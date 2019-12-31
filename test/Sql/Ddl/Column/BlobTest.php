<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Ddl\Column;

use Laminas\Db\Sql\Ddl\Column\Blob;

class BlobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Blob::setLength
     */
    public function testSetLength()
    {
        $blob = new Blob('foo', 55);
        $this->assertEquals(55, $blob->getLength());
        $this->assertSame($blob, $blob->setLength(20));
        $this->assertEquals(20, $blob->getLength());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Blob::getLength
     */
    public function testGetLength()
    {
        $blob = new Blob('foo', 55);
        $this->assertEquals(55, $blob->getLength());
    }

    /**
     * @covers Laminas\Db\Sql\Ddl\Column\Blob::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Blob('foo', 10000000);
        $this->assertEquals(
            array(array('%s %s', array('foo', 'BLOB 10000000 NOT NULL'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );
    }
}
