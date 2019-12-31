<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql;

use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\StatementContainer;
use Laminas\Db\Sql\Combine;
use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Db\Sql\Select;

class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Combine
     */
    protected $combine;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->combine = new Combine;
    }

    public function testRejectsInvalidStatement()
    {
        $this->setExpectedException('Laminas\Db\Sql\Exception\InvalidArgumentException');

        $this->combine->combine('foo');
    }

    public function testGetSqlString()
    {
        $this->combine
                ->union(new Select('t1'))
                ->intersect(new Select('t2'))
                ->except(new Select('t3'))
                ->union(new Select('t4'));

        $this->assertEquals(
            '(SELECT "t1".* FROM "t1") INTERSECT (SELECT "t2".* FROM "t2") EXCEPT (SELECT "t3".* FROM "t3") UNION (SELECT "t4".* FROM "t4")',
            $this->combine->getSqlString()
        );
    }

    public function testGetSqlStringWithModifier()
    {
        $this->combine
                ->union(new Select('t1'))
                ->union(new Select('t2'), 'ALL');

        $this->assertEquals(
            '(SELECT "t1".* FROM "t1") UNION ALL (SELECT "t2".* FROM "t2")',
            $this->combine->getSqlString()
        );
    }

    public function testGetSqlStringFromArray()
    {
        $this->combine->combine(array(
            array(new Select('t1')),
            array(new Select('t2'), Combine::COMBINE_INTERSECT, 'ALL'),
            array(new Select('t3'), Combine::COMBINE_EXCEPT),
        ));

        $this->assertEquals(
            '(SELECT "t1".* FROM "t1") INTERSECT ALL (SELECT "t2".* FROM "t2") EXCEPT (SELECT "t3".* FROM "t3")',
            $this->combine->getSqlString()
        );

        $this->combine = new Combine();
        $this->combine->combine(array(
            new Select('t1'),
            new Select('t2'),
            new Select('t3'),
        ));

        $this->assertEquals(
            '(SELECT "t1".* FROM "t1") UNION (SELECT "t2".* FROM "t2") UNION (SELECT "t3".* FROM "t3")',
            $this->combine->getSqlString()
        );
    }

    public function testGetSqlStringEmpty()
    {
        $this->assertSame(
            null,
            $this->combine->getSqlString()
        );
    }

    public function testPrepareStatementWithModifier()
    {
        $select1 = new Select('t1');
        $select1->where(array('x1'=>10));
        $select2 = new Select('t2');
        $select2->where(array('x2'=>20));

        $this->combine->combine(array(
            $select1,
            $select2
        ));

        $adapter = $this->getMockAdapter();

        $statement = $this->combine->prepareStatement($adapter, new StatementContainer);
        $this->assertInstanceOf('Laminas\Db\Adapter\StatementContainerInterface', $statement);
        $this->assertEquals(
            '(SELECT "t1".* FROM "t1" WHERE "x1" = ?) UNION (SELECT "t2".* FROM "t2" WHERE "x2" = ?)',
            $statement->getSql()
        );
    }

    public function testAlignColumns()
    {
        $select1 = new Select('t1');
        $select1->columns(array(
            'c0' => 'c0',
            'c1' => 'c1',
        ));
        $select2 = new Select('t2');
        $select2->columns(array(
            'c1' => 'c1',
            'c2' => 'c2',
        ));

        $this->combine
                ->union(array($select1, $select2))
                ->alignColumns();

        $this->assertEquals(
            array(
                'c0' => 'c0',
                'c1' => 'c1',
                'c2' => new Expression('NULL'),
            ),
            $select1->getRawState('columns')
        );

        $this->assertEquals(
            array(
                'c0' => new Expression('NULL'),
                'c1' => 'c1',
                'c2' => 'c2',
            ),
            $select2->getRawState('columns')
        );
    }

    public function testGetRawState()
    {
        $select = new Select('t1');
        $this->combine->combine($select);
        $this->assertSame(
            array(
                'combine' => array(
                    array(
                        'select'   => $select,
                        'type'     => Combine::COMBINE_UNION,
                        'modifier' => ''
                    ),
                ),
                'columns' => array(
                    '0' => '*',
                ),
            ),
            $this->combine->getRawState()
        );
    }

    /**
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Laminas\Db\Adapter\Adapter
     */
    protected function getMockAdapter()
    {
        $parameterContainer = new ParameterContainer();

        $mockStatement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($parameterContainer));


        $setGetSqlFunction = function ($sql = null) use ($mockStatement) {
            static $sqlValue;
            if ($sql) {
                $sqlValue = $sql;
                return $mockStatement;
            }
            return $sqlValue;
        };
        $mockStatement->expects($this->any())->method('setSql')->will($this->returnCallback($setGetSqlFunction));
        $mockStatement->expects($this->any())->method('getSql')->will($this->returnCallback($setGetSqlFunction));

        $mockDriver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));

        return $this->getMock('Laminas\Db\Adapter\Adapter', null, array($mockDriver));
    }
}
