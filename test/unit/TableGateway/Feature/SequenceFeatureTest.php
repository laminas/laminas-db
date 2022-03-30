<?php

namespace LaminasTest\Db\TableGateway\Feature;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\TableGateway\Feature\SequenceFeature;
use Laminas\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;

class SequenceFeatureTest extends TestCase
{
    /** @var SequenceFeature */
    protected $feature;

    /** @var TableGateway */
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
    public function testNextSequenceId(string $platformName, string $statementSql)
    {
        $platform = $this->createMock(PlatformInterface::class);
        $platform->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($platformName));
        $platform->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnValue($this->sequenceName));
        $adapter = $this->getMockBuilder(Adapter::class)
            ->setMethods(['getPlatform', 'createStatement'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($platform));
        $result = $this->createMock(ResultInterface::class);
        $result->expects($this->any())
            ->method('current')
            ->will($this->returnValue(['nextval' => 2]));
        $statement = $this->createMock(StatementInterface::class);
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
            TableGateway::class,
            ['table', $adapter],
            '',
            true
        );
        $this->feature->setTableGateway($this->tableGateway);
        $this->feature->nextSequenceId();
    }

    /** @psalm-return array<array-key, array{0: string, 1: string}> */
    public function nextSequenceIdProvider(): array
    {
        return [
            ['PostgreSQL', 'SELECT NEXTVAL(\'"' . $this->sequenceName . '"\')'],
            ['Oracle', 'SELECT ' . $this->sequenceName . '.NEXTVAL as "nextval" FROM dual'],
        ];
    }
}
