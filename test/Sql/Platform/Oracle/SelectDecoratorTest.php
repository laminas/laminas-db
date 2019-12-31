<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Platform\Oracle;

use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\Oracle as OraclePlatform;
use Laminas\Db\Sql\Platform\Oracle\SelectDecorator;
use Laminas\Db\Sql\Select;

class SelectDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox integration test: Testing SelectDecorator will use Select to produce properly Oracle dialect prepared sql
     * @covers Laminas\Db\Sql\Platform\SqlServer\SelectDecorator::prepareStatement
     * @covers Laminas\Db\Sql\Platform\SqlServer\SelectDecorator::processLimitOffset
     * @dataProvider dataProvider
     */
    public function testPrepareStatement(Select $select, $expectedSql, $expectedParams, $notUsed, $expectedFormatParamCount)
    {
        $driver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $driver->expects($this->exactly($expectedFormatParamCount))->method('formatParameterName')->will($this->returnValue('?'));

        // test
        $adapter = $this->getMock(
            'Laminas\Db\Adapter\Adapter',
            null,
            array(
                $driver,
                new OraclePlatform()
            )
        );

        $parameterContainer = new ParameterContainer;
        $statement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $statement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($parameterContainer));

        $statement->expects($this->once())->method('setSql')->with($expectedSql);

        $selectDecorator = new SelectDecorator;
        $selectDecorator->setSubject($select);
        $selectDecorator->prepareStatement($adapter, $statement);

        $this->assertEquals($expectedParams, $parameterContainer->getNamedArray());
    }

    /**
     * @testdox integration test: Testing SelectDecorator will use Select to produce properly Oracle dialect sql statements
     * @covers Laminas\Db\Sql\Platform\Oracle\SelectDecorator::getSqlString
     * @dataProvider dataProvider
     */
    public function testGetSqlString(Select $select, $notUsed, $notUsed, $expectedSql)
    {
        $parameterContainer = new ParameterContainer;
        $statement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $statement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($parameterContainer));

        $selectDecorator = new SelectDecorator;
        $selectDecorator->setSubject($select);
        $this->assertEquals($expectedSql, $selectDecorator->getSqlString(new OraclePlatform));
    }

    /**
     * Data provider for testGetSqlString
     *
     * @return array
     */
    public function dataProvider()
    {
        $select0 = new Select;
        $select0->from(array('x' => 'foo'));
        $expectedSql0 = 'SELECT "x".* FROM "foo" "x"';
        $expectedFormatParamCount0 = 0;

        $select1a = new Select('test');
        $select1b = new Select(array('a' => $select1a));
        $select1 = new Select(array('b' => $select1b));
        $expectedSql1 = 'SELECT "b".* FROM (SELECT "a".* FROM (SELECT "test".* FROM "test") "a") "b"';
        $expectedFormatParamCount1 = 0;

        return array(
            array($select0, $expectedSql0, array(), $expectedSql0, $expectedFormatParamCount0),
            array($select1, $expectedSql1, array(), $expectedSql1, $expectedFormatParamCount1),
        );
    }
}
