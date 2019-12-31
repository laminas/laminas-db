<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\ResultSet;

class AbstractResultSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultSet;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::initialize
     */
    public function testInitialize()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');

        $this->assertSame($resultSet, $resultSet->initialize(array(
            array('id' => 1, 'name' => 'one'),
            array('id' => 2, 'name' => 'two'),
            array('id' => 3, 'name' => 'three'),
        )));

        $this->setExpectedException(
            'Laminas\Db\ResultSet\Exception\InvalidArgumentException',
            'DataSource provided is not an array, nor does it implement Iterator or IteratorAggregate'
        );
        $resultSet->initialize('foo');
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::buffer
     */
    public function testBuffer()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $this->assertSame($resultSet, $resultSet->buffer());

        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator(array(
                array('id' => 1, 'name' => 'one'),
                array('id' => 2, 'name' => 'two'),
                array('id' => 3, 'name' => 'three'),
        )));
        $resultSet->next(); // start iterator
        $this->setExpectedException(
            'Laminas\Db\ResultSet\Exception\RuntimeException',
            'Buffering must be enabled before iteration is started'
        );
        $resultSet->buffer();
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::isBuffered
     */
    public function testIsBuffered()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $this->assertFalse($resultSet->isBuffered());
        $resultSet->buffer();
        $this->assertTrue($resultSet->isBuffered());
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::getDataSource
     */
    public function testGetDataSource()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator(array(
            array('id' => 1, 'name' => 'one'),
            array('id' => 2, 'name' => 'two'),
            array('id' => 3, 'name' => 'three'),
        )));
        $this->assertInstanceOf('\ArrayIterator', $resultSet->getDataSource());
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::getFieldCount
     */
    public function testGetFieldCount()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator(array(
            array('id' => 1, 'name' => 'one'),
        )));
        $this->assertEquals(2, $resultSet->getFieldCount());
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::next
     */
    public function testNext()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator(array(
            array('id' => 1, 'name' => 'one'),
            array('id' => 2, 'name' => 'two'),
            array('id' => 3, 'name' => 'three'),
        )));
        $this->assertNull($resultSet->next());
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::key
     */
    public function testKey()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator(array(
            array('id' => 1, 'name' => 'one'),
            array('id' => 2, 'name' => 'two'),
            array('id' => 3, 'name' => 'three'),
        )));
        $resultSet->next();
        $this->assertEquals(1, $resultSet->key());
        $resultSet->next();
        $this->assertEquals(2, $resultSet->key());
        $resultSet->next();
        $this->assertEquals(3, $resultSet->key());
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::current
     */
    public function testCurrent()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator(array(
            array('id' => 1, 'name' => 'one'),
            array('id' => 2, 'name' => 'two'),
            array('id' => 3, 'name' => 'three'),
        )));
        $this->assertEquals(array('id' => 1, 'name' => 'one'), $resultSet->current());
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::valid
     */
    public function testValid()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator(array(
            array('id' => 1, 'name' => 'one'),
            array('id' => 2, 'name' => 'two'),
            array('id' => 3, 'name' => 'three'),
        )));
        $this->assertTrue($resultSet->valid());
        $resultSet->next(); $resultSet->next(); $resultSet->next();
        $this->assertFalse($resultSet->valid());
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::rewind
     */
    public function testRewind()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator(array(
            array('id' => 1, 'name' => 'one'),
            array('id' => 2, 'name' => 'two'),
            array('id' => 3, 'name' => 'three'),
        )));
        $this->assertNull($resultSet->rewind());
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::count
     */
    public function testCount()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator(array(
            array('id' => 1, 'name' => 'one'),
            array('id' => 2, 'name' => 'two'),
            array('id' => 3, 'name' => 'three'),
        )));
        $this->assertEquals(3, $resultSet->count());
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::toArray
     */
    public function testToArray()
    {
        $resultSet = $this->getMockForAbstractClass('Laminas\Db\ResultSet\AbstractResultSet');
        $resultSet->initialize(new \ArrayIterator(array(
            array('id' => 1, 'name' => 'one'),
            array('id' => 2, 'name' => 'two'),
            array('id' => 3, 'name' => 'three'),
        )));
        $this->assertEquals(
            array(
                array('id' => 1, 'name' => 'one'),
                array('id' => 2, 'name' => 'two'),
                array('id' => 3, 'name' => 'three'),
            ),
            $resultSet->toArray()
        );
    }
}
