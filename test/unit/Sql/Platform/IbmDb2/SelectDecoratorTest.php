<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Platform\IbmDb2;

use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\IbmDb2 as IbmDb2Platform;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Platform\IbmDb2\SelectDecorator;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use PHPUnit\Framework\TestCase;

class SelectDecoratorTest extends TestCase
{
    /**
     * @testdox integration test: Testing SelectDecorator will use Select to produce properly IBM Db2
     *                            dialect prepared sql
     * @covers \Laminas\Db\Sql\Platform\SqlServer\SelectDecorator::prepareStatement
     * @covers \Laminas\Db\Sql\Platform\SqlServer\SelectDecorator::processLimitOffset
     * @dataProvider dataProvider
     */
    public function testPrepareStatement(
        Select $select,
        $expectedPrepareSql,
        $expectedParams,
        $notUsed,
        $supportsLimitOffset
    ) {
        $driver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $driver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));

        // test
        $adapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods()
            ->setConstructorArgs([
                $driver,
                new IbmDb2Platform(),
            ])
            ->getMock();

        $parameterContainer = new ParameterContainer;
        $statement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();

        $statement->expects($this->any())->method('getParameterContainer')
            ->will($this->returnValue($parameterContainer));
        $statement->expects($this->once())->method('setSql')->with($expectedPrepareSql);

        $selectDecorator = new SelectDecorator;
        $selectDecorator->setSubject($select);
        $selectDecorator->setSupportsLimitOffset($supportsLimitOffset);
        $selectDecorator->prepareStatement($adapter, $statement);

        self::assertEquals($expectedParams, $parameterContainer->getNamedArray());
    }

    /**
     * @testdox integration test: Testing SelectDecorator will use Select to produce properly Ibm DB2
     *                            dialect sql statements
     * @covers \Laminas\Db\Sql\Platform\IbmDb2\SelectDecorator::getSqlString
     * @dataProvider dataProvider
     */
    public function testGetSqlString(Select $select, $ignored0, $ignored1, $expectedSql, $supportsLimitOffset)
    {
        $parameterContainer = new ParameterContainer;
        $statement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $statement->expects($this->any())->method('getParameterContainer')
            ->will($this->returnValue($parameterContainer));

        $selectDecorator = new SelectDecorator;
        $selectDecorator->setSubject($select);
        $selectDecorator->setSupportsLimitOffset($supportsLimitOffset);

        self::assertEquals($expectedSql, @$selectDecorator->getSqlString(new IbmDb2Platform));
    }

    /**
     * Data provider for testGetSqlString
     *
     * @return array
     */
    public function dataProvider()
    {
        $select0 = new Select;
        $select0->from(['x' => 'foo'])->limit(5);
        $expectedParams0 = [ 'limit' => 5, 'offset' => 0 ];
        // @codingStandardsIgnoreStart
        $expectedPrepareSql0 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN ? AND ?';
        $expectedSql0 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN 0 AND 5';
        // @codingStandardsIgnoreEnd

        $select1 = new Select;
        $select1->from(['x' => 'foo'])->limit(5)->offset(10);
        $expectedParams1 = [ 'limit' => 15, 'offset' => 11 ];
        // @codingStandardsIgnoreStart
        $expectedPrepareSql1 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN ? AND ?';
        $expectedSql1 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN 11 AND 15';
        // @codingStandardsIgnoreEnd

        $select2 = new Select;
        $select2->columns([new Expression('DISTINCT(id) as id')])->from(['x' => 'foo'])->limit(5)->offset(10);
        $expectedParams2 = [ 'limit' => 15, 'offset' => 11];
        // @codingStandardsIgnoreStart
        $expectedPrepareSql2 = 'SELECT DISTINCT(id) as id FROM ( SELECT DISTINCT(id) as id, DENSE_RANK() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN ? AND ?';
        $expectedSql2 = 'SELECT DISTINCT(id) as id FROM ( SELECT DISTINCT(id) as id, DENSE_RANK() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN 11 AND 15';
        // @codingStandardsIgnoreEnd

        $select3 = new Select;
        $where3  = new Where();
        $where3->greaterThan('x.id', '10')->AND->lessThan('x.id', '31');
        $select3->from(['x' => 'foo'])->where($where3)->limit(5)->offset(10);
        $expectedParams3 = [ 'limit' => 15, 'offset' => 11, 'where1' => '10', 'where2' => '31' ];
        // @codingStandardsIgnoreStart
        $expectedPrepareSql3 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" WHERE "x"."id" > ? AND "x"."id" < ? ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN ? AND ?';
        $expectedSql3 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" WHERE "x"."id" > \'10\' AND "x"."id" < \'31\' ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN 11 AND 15';
        // @codingStandardsIgnoreEnd

        $select4 = new Select;
        $where4  = $where3;
        $select4->from(['x' => 'foo'])->where($where4)->limit(5);
        $expectedParams4 = [ 'limit' => 5, 'offset' => 0, 'where1' => 10, 'where2' => 31 ];
        // @codingStandardsIgnoreStart
        $expectedPrepareSql4 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" WHERE "x"."id" > ? AND "x"."id" < ? ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN ? AND ?';
        $expectedSql4 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" WHERE "x"."id" > \'10\' AND "x"."id" < \'31\' ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN 0 AND 5';
        // @codingStandardsIgnoreEnd

        $select5 = new Select;
        $select5->from(['x' => 'foo'])->limit(5);
        $expectedParams5 = [];
        $expectedPrepareSql5 = 'SELECT "x".* FROM "foo" "x" LIMIT 5';
        $expectedSql5 = 'SELECT "x".* FROM "foo" "x" LIMIT 5';

        $select6 = new Select;
        $select6->columns([new Expression('DISTINCT(id) as id')])->from(['x' => 'foo'])->limit(5)->offset(10);
        $expectedParams6 = [];
        $expectedPrepareSql6 = 'SELECT DISTINCT(id) as id FROM "foo" "x" LIMIT 5 OFFSET 10';
        $expectedSql6 = 'SELECT DISTINCT(id) as id FROM "foo" "x" LIMIT 5 OFFSET 10';

        return [
            [$select0, $expectedPrepareSql0, $expectedParams0, $expectedSql0, false],
            [$select1, $expectedPrepareSql1, $expectedParams1, $expectedSql1, false],
            [$select2, $expectedPrepareSql2, $expectedParams2, $expectedSql2, false],
            [$select3, $expectedPrepareSql3, $expectedParams3, $expectedSql3, false],
            [$select4, $expectedPrepareSql4, $expectedParams4, $expectedSql4, false],
            [$select5, $expectedPrepareSql5, $expectedParams5, $expectedSql5, true],
            [$select6, $expectedPrepareSql6, $expectedParams6, $expectedSql6, true],
        ];
    }
}
