<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql;

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\InsertIgnore;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\TableIdentifier;
use LaminasTest\Db\TestAsset\Replace;
use LaminasTest\Db\TestAsset\TrustingSql92Platform;
use PHPUnit\Framework\TestCase;

class InsertIgnoreTest extends TestCase
{
    /**
     * @var InsertIgnore
     */
    protected $insert;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->insert = new InsertIgnore;
    }

    public function testInto()
    {
        $this->insert->into('table');
        self::assertEquals('table', $this->insert->getRawState('table'));

        $tableIdentifier = new TableIdentifier('table', 'schema');
        $this->insert->into($tableIdentifier);
        self::assertEquals($tableIdentifier, $this->insert->getRawState('table'));
    }

    public function testColumns()
    {
        $columns = ['foo', 'bar'];
        $this->insert->columns($columns);
        self::assertEquals($columns, $this->insert->getRawState('columns'));
    }

    public function testValues()
    {
        $this->insert->values(['foo' => 'bar']);
        self::assertEquals(['foo'], $this->insert->getRawState('columns'));
        self::assertEquals(['bar'], $this->insert->getRawState('values'));

        // test will merge cols and values of previously set stuff
        $this->insert->values(['foo' => 'bax'], InsertIgnore::VALUES_MERGE);
        $this->insert->values(['boom' => 'bam'], InsertIgnore::VALUES_MERGE);
        self::assertEquals(['foo', 'boom'], $this->insert->getRawState('columns'));
        self::assertEquals(['bax', 'bam'], $this->insert->getRawState('values'));

        $this->insert->values(['foo' => 'bax']);
        self::assertEquals(['foo'], $this->insert->getRawState('columns'));
        self::assertEquals(['bax'], $this->insert->getRawState('values'));
    }

    public function testValuesThrowsExceptionWhenNotArrayOrSelect()
    {
        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('values() expects an array of values or Laminas\Db\Sql\Select instance');
        $this->insert->values(5);
    }

    public function testValuesThrowsExceptionWhenSelectMergeOverArray()
    {
        $this->insert->values(['foo' => 'bar']);

        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('A Laminas\Db\Sql\Select instance cannot be provided with the merge flag');
        $this->insert->values(new Select, InsertIgnore::VALUES_MERGE);
    }

    public function testValuesThrowsExceptionWhenArrayMergeOverSelect()
    {
        $this->insert->values(new Select);

        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage(
            'An array of values cannot be provided with the merge flag when a Laminas\Db\Sql\Select instance already '
            . 'exists as the value source'
        );
        $this->insert->values(['foo' => 'bar'], InsertIgnore::VALUES_MERGE);
    }

    /**
     * @group Laminas-4926
     */
    public function testEmptyArrayValues()
    {
        $this->insert->values([]);
        self::assertEquals([], $this->readAttribute($this->insert, 'columns'));
    }

    public function testPrepareStatement()
    {
        $mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();

        $mockStatement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $pContainer = new \Laminas\Db\Adapter\ParameterContainer([]);
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($pContainer));
        $mockStatement->expects($this->at(1))
            ->method('setSql')
            ->with($this->equalTo('INSERT IGNORE INTO "foo" ("bar", "boo") VALUES (?, NOW())'));

        $this->insert->into('foo')
            ->values(['bar' => 'baz', 'boo' => new Expression('NOW()')]);

        $this->insert->prepareStatement($mockAdapter, $mockStatement);

        // with TableIdentifier
        $this->insert = new InsertIgnore;
        $mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();

        $mockStatement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $pContainer = new \Laminas\Db\Adapter\ParameterContainer([]);
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($pContainer));
        $mockStatement->expects($this->at(1))
            ->method('setSql')
            ->with($this->equalTo('INSERT IGNORE INTO "sch"."foo" ("bar", "boo") VALUES (?, NOW())'));

        $this->insert->into(new TableIdentifier('foo', 'sch'))
            ->values(['bar' => 'baz', 'boo' => new Expression('NOW()')]);

        $this->insert->prepareStatement($mockAdapter, $mockStatement);
    }

    public function testPrepareStatementWithSelect()
    {
        $mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();

        $mockStatement = new \Laminas\Db\Adapter\StatementContainer();

        $select = new Select('bar');
        $this->insert
                ->into('foo')
                ->columns(['col1'])
                ->select($select->where(['x' => 5]))
                ->prepareStatement($mockAdapter, $mockStatement);

        self::assertEquals(
            'INSERT IGNORE INTO "foo" ("col1") SELECT "bar".* FROM "bar" WHERE "x" = ?',
            $mockStatement->getSql()
        );
        $parameters = $mockStatement->getParameterContainer()->getNamedArray();
        self::assertSame(['subselect1where1' => 5], $parameters);
    }

    public function testGetSqlString()
    {
        $this->insert->into('foo')
            ->values(['bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null]);

        self::assertEquals(
            'INSERT IGNORE INTO "foo" ("bar", "boo", "bam") VALUES (\'baz\', NOW(), NULL)',
            $this->insert->getSqlString(new TrustingSql92Platform())
        );

        // with TableIdentifier
        $this->insert = new InsertIgnore;
        $this->insert->into(new TableIdentifier('foo', 'sch'))
            ->values(['bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null]);

        self::assertEquals(
            'INSERT IGNORE INTO "sch"."foo" ("bar", "boo", "bam") VALUES (\'baz\', NOW(), NULL)',
            $this->insert->getSqlString(new TrustingSql92Platform())
        );

        // with Select
        $this->insert = new InsertIgnore;
        $select = new Select();
        $this->insert->into('foo')->select($select->from('bar'));

        self::assertEquals(
            'INSERT IGNORE INTO "foo"  SELECT "bar".* FROM "bar"',
            $this->insert->getSqlString(new TrustingSql92Platform())
        );

        // with Select and columns
        $this->insert->columns(['col1', 'col2']);
        self::assertEquals(
            'INSERT IGNORE INTO "foo" ("col1", "col2") SELECT "bar".* FROM "bar"',
            $this->insert->getSqlString(new TrustingSql92Platform())
        );
    }

    public function testGetSqlStringUsingColumnsAndValuesMethods()
    {
        // With columns() and values()
        $this->insert
            ->into('foo')
            ->columns(['col1', 'col2', 'col3'])
            ->values(['val1', 'val2', 'val3']);
        self::assertEquals(
            'INSERT IGNORE INTO "foo" ("col1", "col2", "col3") VALUES (\'val1\', \'val2\', \'val3\')',
            $this->insert->getSqlString(new TrustingSql92Platform())
        );
    }

    // @codingStandardsIgnoreStart
    public function test__set()
    {
        // @codingStandardsIgnoreEnd
        $this->insert->foo = 'bar';
        self::assertEquals(['foo'], $this->insert->getRawState('columns'));
        self::assertEquals(['bar'], $this->insert->getRawState('values'));
    }

    // @codingStandardsIgnoreStart
    public function test__unset()
    {
        // @codingStandardsIgnoreEnd
        $this->insert->foo = 'bar';
        self::assertEquals(['foo'], $this->insert->getRawState('columns'));
        self::assertEquals(['bar'], $this->insert->getRawState('values'));
        unset($this->insert->foo);
        self::assertEquals([], $this->insert->getRawState('columns'));
        self::assertEquals([], $this->insert->getRawState('values'));

        $this->insert->foo = null;
        self::assertEquals(['foo'], $this->insert->getRawState('columns'));
        self::assertEquals([null], $this->insert->getRawState('values'));

        unset($this->insert->foo);
        self::assertEquals([], $this->insert->getRawState('columns'));
        self::assertEquals([], $this->insert->getRawState('values'));
    }

    // @codingStandardsIgnoreStart
    public function test__isset()
    {
        // @codingStandardsIgnoreEnd
        $this->insert->foo = 'bar';
        self::assertTrue(isset($this->insert->foo));

        $this->insert->foo = null;
        self::assertTrue(isset($this->insert->foo));
    }

    // @codingStandardsIgnoreStart
    public function test__get()
    {
        // @codingStandardsIgnoreEnd
        $this->insert->foo = 'bar';
        self::assertEquals('bar', $this->insert->foo);

        $this->insert->foo = null;
        self::assertNull($this->insert->foo);
    }

    /**
     * @group Laminas-536
     */
    public function testValuesMerge()
    {
        $this->insert->into('foo')
            ->values(['bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null]);
        $this->insert->into('foo')
            ->values(['qux' => 100], InsertIgnore::VALUES_MERGE);

        self::assertEquals(
            'INSERT IGNORE INTO "foo" ("bar", "boo", "bam", "qux") VALUES (\'baz\', NOW(), NULL, \'100\')',
            $this->insert->getSqlString(new TrustingSql92Platform())
        );
    }

    public function testSpecificationconstantsCouldBeOverridedByExtensionInPrepareStatement()
    {
        $replace = new Replace();

        $mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();

        $mockStatement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $pContainer = new \Laminas\Db\Adapter\ParameterContainer([]);
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($pContainer));
        $mockStatement->expects($this->at(1))
            ->method('setSql')
            ->with($this->equalTo('REPLACE INTO "foo" ("bar", "boo") VALUES (?, NOW())'));

        $replace->into('foo')
            ->values(['bar' => 'baz', 'boo' => new Expression('NOW()')]);

        $replace->prepareStatement($mockAdapter, $mockStatement);

        // with TableIdentifier
        $replace = new Replace();

        $mockDriver = $this->getMockBuilder('Laminas\Db\Adapter\Driver\DriverInterface')->getMock();
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMockBuilder('Laminas\Db\Adapter\Adapter')
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();

        $mockStatement = $this->getMockBuilder('Laminas\Db\Adapter\Driver\StatementInterface')->getMock();
        $pContainer = new \Laminas\Db\Adapter\ParameterContainer([]);
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($pContainer));
        $mockStatement->expects($this->at(1))
            ->method('setSql')
            ->with($this->equalTo('REPLACE INTO "sch"."foo" ("bar", "boo") VALUES (?, NOW())'));

        $replace->into(new TableIdentifier('foo', 'sch'))
            ->values(['bar' => 'baz', 'boo' => new Expression('NOW()')]);

        $replace->prepareStatement($mockAdapter, $mockStatement);
    }

    public function testSpecificationconstantsCouldBeOverridedByExtensionInGetSqlString()
    {
        $replace = new Replace();
        $replace->into('foo')
            ->values(['bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null]);

        self::assertEquals(
            'REPLACE INTO "foo" ("bar", "boo", "bam") VALUES (\'baz\', NOW(), NULL)',
            $replace->getSqlString(new TrustingSql92Platform())
        );

        // with TableIdentifier
        $replace = new Replace();
        $replace->into(new TableIdentifier('foo', 'sch'))
            ->values(['bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null]);

        self::assertEquals(
            'REPLACE INTO "sch"."foo" ("bar", "boo", "bam") VALUES (\'baz\', NOW(), NULL)',
            $replace->getSqlString(new TrustingSql92Platform())
        );
    }
}
