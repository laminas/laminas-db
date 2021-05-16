<?php

namespace LaminasTest\Db\TableGateway\Feature;

use Laminas\Db\TableGateway\Feature\SequenceFeature;
use PHPUnit\Framework\TestCase;

class SequenceFeatureTest extends TestCase
{
    /** @var SequenceFeature */
    protected $feature;

    /** @var \Laminas\Db\TableGateway\TableGateway */
    protected $tableGateway;

    /**  @var string primary key name */
    protected $primaryKeyField = 'id';

    /** @var string  sequence name */
    protected $sequenceName = 'table_sequence';

    protected function setUp(): void
    {
        $this->feature = new SequenceFeature($this->primaryKeyField, $this->sequenceName);
    }

    /**
     * @dataProvider nextSequenceIdProvider
     */
    public function testNextSequenceId($platformName, $statementSql)
    {
        $platform = $this->createMock('Laminas\Db\Adapter\Platform\PlatformInterface');
        $platform->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($platformName));
        $platform->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnValue($this->sequenceName));
        $adapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods(['getPlatform', 'createStatement'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($platform));
        $result = $this->createMock('Laminas\Db\Adapter\Driver\ResultInterface');
        $result->expects($this->any())
            ->method('current')
            ->will($this->returnValue(['nextval' => 2]));
        $statement = $this->createMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));
        $statement->expects($this->any())
            ->method('prepare')
            ->with($statementSql);
        $adapter->expects($this->once())
            ->method('createStatement')
            ->will($this->returnValue($statement));
        $this->tableGateway = $this->getMockForAbstractClass(
            'Laminas\Db\TableGateway\TableGateway',
            ['table', $adapter],
            '',
            true
        );
        $this->feature->setTableGateway($this->tableGateway);
        $this->feature->nextSequenceId();
    }

    public function nextSequenceIdProvider()
    {
        return [
            ['PostgreSQL', 'SELECT NEXTVAL(\'"' . $this->sequenceName . '"\')'],
            ['Oracle', 'SELECT ' . $this->sequenceName . '.NEXTVAL as "nextval" FROM dual'],
        ];
    }
}
