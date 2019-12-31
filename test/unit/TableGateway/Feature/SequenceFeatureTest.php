<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\TableGateway\Feature;

use PHPUnit\Framework\TestCase;
use Zend\Db\TableGateway\Feature\SequenceFeature;

class SequenceFeatureTest extends TestCase
{
    /** @var SequenceFeature */
    protected $feature;

    /** @var \Zend\Db\TableGateway\TableGateway */
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
        $platform = $this->getMockForAbstractClass('Zend\Db\Adapter\Platform\PlatformInterface', ['getName']);
        $platform->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($platformName));
        $platform->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnValue($this->sequenceName));
        $adapter = $this->getMockBuilder('Zend\Db\Adapter\Adapter')
            ->setMethods(['getPlatform', 'createStatement'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($platform));
        $result = $this->getMockForAbstractClass(
            'Zend\Db\Adapter\Driver\ResultInterface',
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
            'Zend\Db\Adapter\Driver\StatementInterface',
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
            'Zend\Db\TableGateway\TableGateway',
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
        $platform = $this->getMockForAbstractClass('Zend\Db\Adapter\Platform\PlatformInterface', ['getName']);
        $platform->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($platformName));
        $platform->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnValue($this->sequenceName));
        $adapter = $this->getMockBuilder('Zend\Db\Adapter\Adapter')
            ->setMethods(['getPlatform', 'createStatement'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($platform));
        $result = $this->getMockForAbstractClass(
            'Zend\Db\Adapter\Driver\ResultInterface',
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
            'Zend\Db\Adapter\Driver\StatementInterface',
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
            'Zend\Db\TableGateway\TableGateway',
            ['table', $adapter],
            '',
            true
        );
        $this->feature->setTableGateway($this->tableGateway);
        $insert = $this->getMockBuilder('Zend\Db\Sql\Insert')
            ->setMethods(['getPlatform', 'createStatement', 'getRawState'])
            ->disableOriginalConstructor()
            ->getMock();
        $insert->expects($this->at(0))
            ->method('getRawState')
            ->with('columns')
            ->will($this->returnValue(['id']));
        /** @var \Zend\Db\Sql\Insert $insert */
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
