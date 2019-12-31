<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Platform\Sqlite;

use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\Sqlite as SqlitePlatform;
use Laminas\Db\Sql\Platform\Sqlite\SelectDecorator;
use Laminas\Db\Sql\Select;
use PHPUnit\Framework\TestCase;

class SelectDecoratorTest extends TestCase
{
    /**
     * @testdox integration test: Testing SelectDecorator will use Select an internal state to prepare a proper combine
     * statement
     * @covers \Laminas\Db\Sql\Platform\Sqlite\SelectDecorator::prepareStatement
     * @covers \Laminas\Db\Sql\Platform\Sqlite\SelectDecorator::processCombine
     * @dataProvider dataProviderUnionSyntaxFromCombine
     */
    public function testPrepareStatementPreparesUnionSyntaxFromCombine(Select $select, $expectedSql, $expectedParams)
    {
        $driver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $driver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));

        // test
        $adapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods()
            ->setConstructorArgs([
                $driver,
                new SqlitePlatform(),
            ])
            ->getMock();

        $parameterContainer = new ParameterContainer;
        $statement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $statement->expects($this->any())->method('getParameterContainer')
            ->will($this->returnValue($parameterContainer));

        $statement->expects($this->once())->method('setSql')->with($expectedSql);

        $selectDecorator = new SelectDecorator;
        $selectDecorator->setSubject($select);
        $selectDecorator->prepareStatement($adapter, $statement);

        self::assertEquals($expectedParams, $parameterContainer->getNamedArray());
    }

    /**
     * @testdox integration test: Testing SelectDecorator will use Select an internal state to prepare a proper combine
     * statement
     * @covers \Laminas\Db\Sql\Platform\Sqlite\SelectDecorator::getSqlString
     * @covers \Laminas\Db\Sql\Platform\Sqlite\SelectDecorator::processCombine
     * @dataProvider dataProviderUnionSyntaxFromCombine
     */
    public function testGetSqlStringPreparesUnionSyntaxFromCombine(Select $select, $ignore, $alsoIgnore, $expectedSql)
    {
        $parameterContainer = new ParameterContainer;
        $statement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $statement->expects($this->any())->method('getParameterContainer')
            ->will($this->returnValue($parameterContainer));

        $selectDecorator = new SelectDecorator;
        $selectDecorator->setSubject($select);
        self::assertEquals($expectedSql, $selectDecorator->getSqlString(new SqlitePlatform));
    }

    /**
     * Create a data provider for union syntax that would come from combine
     *
     * @return mixed[]
     */
    public function dataProviderUnionSyntaxFromCombine()
    {
        $select0 = new Select;
        $select0->from('foo');
        $select1 = clone $select0;
        $select0->combine($select1);

        $expectedPrepareSql0 = ' SELECT "foo".* FROM "foo"  UNION  SELECT "foo".* FROM "foo"';
        $expectedParams0 = [];
        $expectedSql0 = ' SELECT "foo".* FROM "foo"  UNION  SELECT "foo".* FROM "foo"';

        return [
            [$select0, $expectedPrepareSql0, $expectedParams0, $expectedSql0],
        ];
    }
}
