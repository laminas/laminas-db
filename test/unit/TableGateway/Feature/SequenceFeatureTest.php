<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

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

    protected function setUp()
    {
        $this->feature = new SequenceFeature($this->primaryKeyField, $this->sequenceName);
    }

    /**
     * @dataProvider nextSequenceIdProvider
     */
    public function testNextSequenceId($platformName, $statementSql)
    {
        $platform = $this->getMockForAbstractClass('Laminas\Db\Adapter\Platform\PlatformInterface', ['getName']);
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
        $result = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\ResultInterface',
            [],
            '',
            false,
            true,
            true,
            ['current']
        );
        $result->expects($this->any())
            ->method('current')
            ->will($this->returnValue(['nextval' => 2]));
        $statement = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\StatementInterface',
            [],
            '',
            false,
            true,
            true,
            ['prepare', 'execute']
        );
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

    /**
     * @dataProvider lastSequenceIdProvider
     */
    public function testPreInsertWillReturnLastInsertValueIfPrimaryKeySetInColumnsData($platformName, $statementSql)
    {
        $platform = $this->getMockForAbstractClass('Laminas\Db\Adapter\Platform\PlatformInterface', ['getName']);
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
        $result = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\ResultInterface',
            [],
            '',
            false,
            true,
            true,
            ['current']
        );
        $result->expects($this->any())
            ->method('current')
            ->will($this->returnValue(['currval' => 1]));
        $statement = $this->getMockForAbstractClass(
            'Laminas\Db\Adapter\Driver\StatementInterface',
            [],
            '',
            false,
            true,
            true,
            ['prepare', 'execute']
        );
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));
        $statement->expects($this->any())
            ->method('prepare')
            ->with($statementSql);
        $adapter->expects($this->any())
            ->method('createStatement')
            ->will($this->returnValue($statement));
        $this->tableGateway = $this->getMockForAbstractClass(
            'Laminas\Db\TableGateway\TableGateway',
            ['table', $adapter],
            '',
            true
        );
        $this->feature->setTableGateway($this->tableGateway);
        $insert = $this->getMockBuilder('Laminas\Db\Sql\Insert')
            ->setMethods(['getPlatform', 'createStatement', 'getRawState'])
            ->disableOriginalConstructor()
            ->getMock();
        $insert->expects($this->at(0))
            ->method('getRawState')
            ->with('columns')
            ->will($this->returnValue(['id']));
        /** @var \Laminas\Db\Sql\Insert $insert */
        $this->feature->preInsert($insert);
        $this->assertEquals(1, $this->tableGateway->getLastInsertValue());
    }

    public function nextSequenceIdProvider()
    {
        return [
            ['PostgreSQL', 'SELECT NEXTVAL(\'"' . $this->sequenceName . '"\')'],
            ['Oracle', 'SELECT ' . $this->sequenceName . '.NEXTVAL as "nextval" FROM dual'],
        ];
    }

    public function lastSequenceIdProvider()
    {
        return [
            ['PostgreSQL', 'SELECT CURRVAL(\'' . $this->sequenceName . '\')'],
            ['Oracle', 'SELECT ' . $this->sequenceName . '.CURRVAL as "currval" FROM dual'],
        ];
    }
}
