<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql;

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\TableIdentifier;

class InsertTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Insert
     */
    protected $insert;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->insert = new Insert;
    }

    /**
     * @covers Laminas\Db\Sql\Insert::into
     */
    public function testInto()
    {
        $this->insert->into('table', 'schema');
        $this->assertEquals('table', $this->readAttribute($this->insert, 'table'));

        $tableIdentifier = new TableIdentifier('table', 'schema');
        $this->insert->into($tableIdentifier);
        $this->assertEquals($tableIdentifier, $this->readAttribute($this->insert, 'table'));
    }

    /**
     * @covers Laminas\Db\Sql\Insert::columns
     */
    public function testColumns()
    {
        $this->insert->columns(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $this->readAttribute($this->insert, 'columns'));
    }

    /**
     * @covers Laminas\Db\Sql\Insert::values
     */
    public function testValues()
    {
        $this->insert->values(array('foo' => 'bar'));
        $this->assertEquals(array('foo'), $this->readAttribute($this->insert, 'columns'));
        $this->assertEquals(array('bar'), $this->readAttribute($this->insert, 'values'));

        // test will merge cols and values of previously set stuff
        $this->insert->values(array('foo' => 'bax'), Insert::VALUES_MERGE);
        $this->insert->values(array('boom' => 'bam'), Insert::VALUES_MERGE);
        $this->assertEquals(array('foo', 'boom'), $this->readAttribute($this->insert, 'columns'));
        $this->assertEquals(array('bax', 'bam'), $this->readAttribute($this->insert, 'values'));

        $this->insert->values(array('foo' => 'bax'));
        $this->assertEquals(array('foo'), $this->readAttribute($this->insert, 'columns'));
        $this->assertEquals(array('bax'), $this->readAttribute($this->insert, 'values'));
    }


    /**
     * @covers Laminas\Db\Sql\Insert::prepareStatement
     */
    public function testPrepareStatement()
    {
        $mockDriver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMock('Laminas\Db\Adapter\Adapter', null, array($mockDriver));

        $mockStatement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $pContainer = new \Laminas\Db\Adapter\ParameterContainer(array());
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($pContainer));
        $mockStatement->expects($this->at(1))
            ->method('setSql')
            ->with($this->equalTo('INSERT INTO "foo" ("bar", "boo") VALUES (?, NOW())'));

        $this->insert->into('foo')
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()')));

        $this->insert->prepareStatement($mockAdapter, $mockStatement);

        // with TableIdentifier
        $this->insert = new Insert;
        $mockDriver = $this->getMock('Laminas\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMock('Laminas\Db\Adapter\Adapter', null, array($mockDriver));

        $mockStatement = $this->getMock('Laminas\Db\Adapter\Driver\StatementInterface');
        $pContainer = new \Laminas\Db\Adapter\ParameterContainer(array());
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($pContainer));
        $mockStatement->expects($this->at(1))
            ->method('setSql')
            ->with($this->equalTo('INSERT INTO "sch"."foo" ("bar", "boo") VALUES (?, NOW())'));

        $this->insert->into(new TableIdentifier('foo', 'sch'))
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()')));

        $this->insert->prepareStatement($mockAdapter, $mockStatement);
    }

    /**
     * @covers Laminas\Db\Sql\Insert::getSqlString
     */
    public function testGetSqlString()
    {
        $this->insert->into('foo')
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null));

        $this->assertEquals('INSERT INTO "foo" ("bar", "boo", "bam") VALUES (\'baz\', NOW(), NULL)', $this->insert->getSqlString());

        // with TableIdentifier
        $this->insert = new Insert;
        $this->insert->into(new TableIdentifier('foo', 'sch'))
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null));

        $this->assertEquals('INSERT INTO "sch"."foo" ("bar", "boo", "bam") VALUES (\'baz\', NOW(), NULL)', $this->insert->getSqlString());
    }

    /**
     * @covers Laminas\Db\Sql\Insert::__set
     */
    public function test__set()
    {
        $this->insert->foo = 'bar';
        $this->assertEquals(array('foo'), $this->readAttribute($this->insert, 'columns'));
        $this->assertEquals(array('bar'), $this->readAttribute($this->insert, 'values'));
    }

    /**
     * @covers Laminas\Db\Sql\Insert::__unset
     */
    public function test__unset()
    {
        $this->insert->foo = 'bar';
        $this->assertEquals(array('foo'), $this->readAttribute($this->insert, 'columns'));
        $this->assertEquals(array('bar'), $this->readAttribute($this->insert, 'values'));
        unset($this->insert->foo);
        $this->assertEquals(array(), $this->readAttribute($this->insert, 'columns'));
        $this->assertEquals(array(), $this->readAttribute($this->insert, 'values'));
    }

    /**
     * @covers Laminas\Db\Sql\Insert::__isset
     */
    public function test__isset()
    {
        $this->insert->foo = 'bar';
        $this->assertTrue(isset($this->insert->foo));
    }

    /**
     * @covers Laminas\Db\Sql\Insert::__get
     */
    public function test__get()
    {
        $this->insert->foo = 'bar';
        $this->assertEquals('bar', $this->insert->foo);
    }

    /**
     * @group Laminas-536
     */
    public function testValuesMerge()
    {
        $this->insert->into('foo')
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null));
        $this->insert->into('foo')
            ->values(array('qux' => 100), Insert::VALUES_MERGE);

        $this->assertEquals('INSERT INTO "foo" ("bar", "boo", "bam", "qux") VALUES (\'baz\', NOW(), NULL, \'100\')', $this->insert->getSqlString());

    }

}
