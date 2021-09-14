<?php

namespace LaminasTest\Db\ResultSet;

use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\AbstractResultSet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractResultSetIntegrationTest extends TestCase
{
    /** @var AbstractResultSet|MockObject */
    protected $resultSet;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->resultSet = $this->getMockForAbstractClass(AbstractResultSet::class);
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::current
     */
    public function testCurrentCallsDataSourceCurrentAsManyTimesWithoutBuffer()
    {
        $result = $this->getMockBuilder(ResultInterface::class)->getMock();
        $this->resultSet->initialize($result);
        $result->expects($this->exactly(3))->method('current')->will($this->returnValue(['foo' => 'bar']));
        $value1 = $this->resultSet->current();
        $value2 = $this->resultSet->current();
        $this->resultSet->current();
        self::assertEquals($value1, $value2);
    }

    /**
     * @covers \Laminas\Db\ResultSet\AbstractResultSet::current
     */
    public function testCurrentCallsDataSourceCurrentOnceWithBuffer()
    {
        $result = $this->getMockBuilder(ResultInterface::class)->getMock();
        $this->resultSet->buffer();
        $this->resultSet->initialize($result);
        $result->expects($this->once())->method('current')->will($this->returnValue(['foo' => 'bar']));
        $value1 = $this->resultSet->current();
        $value2 = $this->resultSet->current();
        $this->resultSet->current();
        self::assertEquals($value1, $value2);
    }
}
