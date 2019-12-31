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

class SelectDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox integration test: Testing SelectDecorator will use Select to produce properly IBM Db2 dialect prepared sql
     * @covers Laminas\Db\Sql\Platform\SqlServer\SelectDecorator::prepareStatement
     * @covers Laminas\Db\Sql\Platform\SqlServer\SelectDecorator::processLimitOffset
     * @dataProvider dataProvider
     */
    public function testPrepareStatement(Select $select, $expectedPrepareSql, $expectedParams, $notUsed)
    {
        $driver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $driver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));

        // test
        $adapter = $this->getMock(
            'Laminas\Db\Adapter\Adapter',
            null,
            array(
                $driver,
                new IbmDb2Platform()
            )
        );

        $parameterContainer = new ParameterContainer;
        $statement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');

        $statement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($parameterContainer));
        $statement->expects($this->once())->method('setSql')->with($expectedPrepareSql);

        $selectDecorator = new SelectDecorator;
        $selectDecorator->setSubject($select);
        $selectDecorator->prepareStatement($adapter, $statement);

        $this->assertEquals($expectedParams, $parameterContainer->getNamedArray());
    }

    /**
     * @testdox integration test: Testing SelectDecorator will use Select to produce properly Ibm DB2 dialect sql statements
     * @covers Laminas\Db\Sql\Platform\IbmDb2\SelectDecorator::getSqlString
     * @dataProvider dataProvider
     */
    public function testGetSqlString(Select $select, $ignored0, $ignored1, $expectedSql)
    {
        $parameterContainer = new ParameterContainer;
        $statement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $statement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($parameterContainer));

        $selectDecorator = new SelectDecorator;
        $selectDecorator->setSubject($select);

        $this->assertEquals($expectedSql, @$selectDecorator->getSqlString(new IbmDb2Platform));
    }

    /**
     * Data provider for testGetSqlString
     *
     * @return array
     */
    public function dataProvider()
    {
        $select0 = new Select;
        $select0->from(array('x' => 'foo'))->limit(5);
        $expectedParams0 = array( 'limit' => 5, 'offset' => 0 );
        $expectedPrepareSql0 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN ? AND ?';
        $expectedSql0 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN 0 AND 5';

        $select1 = new Select;
        $select1->from(array('x' => 'foo'))->limit(5)->offset(10);
        $expectedParams1 = array( 'limit' => 15, 'offset' => 11 );
        $expectedPrepareSql1 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN ? AND ?';
        $expectedSql1 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN 11 AND 15';

        $select2 = new Select;
        $select2->columns(array(new Expression('DISTINCT(id) as id')))->from(array('x' => 'foo'))->limit(5)->offset(10);
        $expectedParams2 = array( 'limit' => 15, 'offset' => 11);
        $expectedPrepareSql2 = 'SELECT DISTINCT(id) as id FROM ( SELECT DISTINCT(id) as id, DENSE_RANK() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN ? AND ?';
        $expectedSql2 = 'SELECT DISTINCT(id) as id FROM ( SELECT DISTINCT(id) as id, DENSE_RANK() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN 11 AND 15';

        $select3 = new Select;
        $where3  = new Where();
        $where3->greaterThan('x.id', '10')->AND->lessThan('x.id', '31');
        $select3->from(array('x' => 'foo'))->where($where3)->limit(5)->offset(10);
        $expectedParams3 = array( 'limit' => 15, 'offset' => 11, 'where1' => '10', 'where2' => '31' );
        $expectedPrepareSql3 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" WHERE "x"."id" > ? AND "x"."id" < ? ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN ? AND ?';
        $expectedSql3 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" WHERE "x"."id" > \'10\' AND "x"."id" < \'31\' ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN 11 AND 15';

        $select4 = new Select;
        $where4  = $where3;
        $select4->from(array('x' => 'foo'))->where($where4)->limit(5);
        $expectedParams4 = array( 'limit' => 5, 'offset' => 0, 'where1' => 10, 'where2' => 31 );
        $expectedPrepareSql4 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" WHERE "x"."id" > ? AND "x"."id" < ? ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN ? AND ?';
        $expectedSql4 = 'SELECT * FROM ( SELECT "x".*, ROW_NUMBER() OVER () AS LAMINAS_DB_ROWNUM FROM "foo" "x" WHERE "x"."id" > \'10\' AND "x"."id" < \'31\' ) AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN 0 AND 5';

        return array(
            array($select0, $expectedPrepareSql0, $expectedParams0, $expectedSql0),
            array($select1, $expectedPrepareSql1, $expectedParams1, $expectedSql1),
            array($select2, $expectedPrepareSql2, $expectedParams2, $expectedSql2),
            array($select3, $expectedPrepareSql3, $expectedParams3, $expectedSql3),
            array($select4, $expectedPrepareSql4, $expectedParams4, $expectedSql4),
        );
    }
}
