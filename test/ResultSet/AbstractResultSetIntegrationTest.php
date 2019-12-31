<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\ResultSet;

class AbstractResultSetIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Laminas\Db\ResultSet\AbstractResultSet|\PHPUnit_Framework_MockObject_MockObject
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
     * @covers Laminas\Db\ResultSet\AbstractResultSet::current
     */
    public function testCurrentCallsDataSourceCurrentAsManyTimesWithoutBuffer()
    {
        $result = $this->getMock('Laminas\Db\Adapter\Driver\ResultInterface');
        $this->resultSet->initialize($result);
        $result->expects($this->exactly(3))->method('current')->will($this->returnValue(array('foo' => 'bar')));
        $value1 = $this->resultSet->current();
        $value2 = $this->resultSet->current();
        $this->resultSet->current();
        $this->assertEquals($value1, $value2);
    }

    /**
     * @covers Laminas\Db\ResultSet\AbstractResultSet::current
     */
    public function testCurrentCallsDataSourceCurrentOnceWithBuffer()
    {
        $result = $this->getMock('Laminas\Db\Adapter\Driver\ResultInterface');
        $this->resultSet->buffer();
        $this->resultSet->initialize($result);
        $result->expects($this->once())->method('current')->will($this->returnValue(array('foo' => 'bar')));
        $value1 = $this->resultSet->current();
        $value2 = $this->resultSet->current();
        $this->resultSet->current();
        $this->assertEquals($value1, $value2);
    }
}
