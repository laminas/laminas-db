<?php

namespace LaminasTest\Db\Sql;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Db\Sql\Predicate\In;
use Laminas\Db\Sql\Predicate\IsNotNull;
use Laminas\Db\Sql\Predicate\IsNull;
use Laminas\Db\Sql\Predicate\Literal;
use Laminas\Db\Sql\Predicate\Operator;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\Sql\Where;
use LaminasTest\Db\DeprecatedAssertionsTrait;
use LaminasTest\Db\TestAsset\DeleteIgnore;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    use DeprecatedAssertionsTrait;

    /** @var Delete */
    protected $delete;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->delete = new Delete();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    /**
     * @covers \Laminas\Db\Sql\Delete::from
     */
    public function testFrom()
    {
        $this->delete->from('foo', 'bar');
        self::assertEquals('foo', $this->readAttribute($this->delete, 'table'));

        $tableIdentifier = new TableIdentifier('foo', 'bar');
        $this->delete->from($tableIdentifier);
        self::assertEquals($tableIdentifier, $this->readAttribute($this->delete, 'table'));
    }

    /**
     * @covers \Laminas\Db\Sql\Delete::where
     * @todo REMOVE THIS IN 3.x
     */
    public function testWhere()
    {
        $this->delete->where('x = y');
        $this->delete->where(['foo > ?' => 5]);
        $this->delete->where(['id' => 2]);
        $this->delete->where(['a = b'], Where::OP_OR);
        $this->delete->where(['c1' => null]);
        $this->delete->where(['c2' => [1, 2, 3]]);
        $this->delete->where([new IsNotNull('c3')]);
        $this->delete->where(['one' => 1, 'two' => 2]);
        $where = $this->delete->where;

        $predicates = $this->readAttribute($where, 'predicates');
        self::assertEquals('AND', $predicates[0][0]);
        self::assertInstanceOf(Literal::class, $predicates[0][1]);

        self::assertEquals('AND', $predicates[1][0]);
        self::assertInstanceOf(Expression::class, $predicates[1][1]);

        self::assertEquals('AND', $predicates[2][0]);
        self::assertInstanceOf(Operator::class, $predicates[2][1]);

        self::assertEquals('OR', $predicates[3][0]);
        self::assertInstanceOf(Literal::class, $predicates[3][1]);

        self::assertEquals('AND', $predicates[4][0]);
        self::assertInstanceOf(IsNull::class, $predicates[4][1]);

        self::assertEquals('AND', $predicates[5][0]);
        self::assertInstanceOf(In::class, $predicates[5][1]);

        self::assertEquals('AND', $predicates[6][0]);
        self::assertInstanceOf(IsNotNull::class, $predicates[6][1]);

        self::assertEquals('AND', $predicates[7][0]);
        self::assertInstanceOf(Operator::class, $predicates[7][1]);

        self::assertEquals('AND', $predicates[8][0]);
        self::assertInstanceOf(Operator::class, $predicates[8][1]);

        $where = new Where();
        $this->delete->where($where);
        self::assertSame($where, $this->delete->where);

        $this->delete->where(function ($what) use ($where) {
            self::assertSame($where, $what);
        });
    }

    /**
     * @covers \Laminas\Db\Sql\Delete::prepareStatement
     */
    public function testPrepareStatement()
    {
        $mockDriver  = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockAdapter = $this->getMockBuilder(Adapter::class)
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();

        $mockStatement = $this->getMockBuilder(StatementInterface::class)->getMock();
        $mockStatement->expects($this->at(2))
            ->method('setSql')
            ->with($this->equalTo('DELETE FROM "foo" WHERE x = y'));

        $this->delete->from('foo')
            ->where('x = y');

        $this->delete->prepareStatement($mockAdapter, $mockStatement);

        // with TableIdentifier
        $this->delete = new Delete();
        $mockDriver   = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockAdapter  = $this->getMockBuilder(Adapter::class)
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();

        $mockStatement = $this->getMockBuilder(StatementInterface::class)->getMock();
        $mockStatement->expects($this->at(2))
            ->method('setSql')
            ->with($this->equalTo('DELETE FROM "sch"."foo" WHERE x = y'));

        $this->delete->from(new TableIdentifier('foo', 'sch'))
            ->where('x = y');

        $this->delete->prepareStatement($mockAdapter, $mockStatement);
    }

    /**
     * @covers \Laminas\Db\Sql\Delete::getSqlString
     */
    public function testGetSqlString()
    {
        $this->delete->from('foo')
            ->where('x = y');
        self::assertEquals('DELETE FROM "foo" WHERE x = y', $this->delete->getSqlString());

        // with TableIdentifier
        $this->delete = new Delete();
        $this->delete->from(new TableIdentifier('foo', 'sch'))
            ->where('x = y');
        self::assertEquals('DELETE FROM "sch"."foo" WHERE x = y', $this->delete->getSqlString());
    }

    /**
     * @coversNothing
     */
    public function testSpecificationconstantsCouldBeOverridedByExtensionInPrepareStatement()
    {
        $deleteIgnore = new DeleteIgnore();

        $mockDriver  = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockAdapter = $this->getMockBuilder(Adapter::class)
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();

        $mockStatement = $this->getMockBuilder(StatementInterface::class)->getMock();
        $mockStatement->expects($this->at(2))
            ->method('setSql')
            ->with($this->equalTo('DELETE IGNORE FROM "foo" WHERE x = y'));

        $deleteIgnore->from('foo')
            ->where('x = y');

        $deleteIgnore->prepareStatement($mockAdapter, $mockStatement);

        // with TableIdentifier
        $deleteIgnore = new DeleteIgnore();

        $mockDriver  = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockAdapter = $this->getMockBuilder(Adapter::class)
            ->setMethods()
            ->setConstructorArgs([$mockDriver])
            ->getMock();

        $mockStatement = $this->getMockBuilder(StatementInterface::class)->getMock();
        $mockStatement->expects($this->at(2))
            ->method('setSql')
            ->with($this->equalTo('DELETE IGNORE FROM "sch"."foo" WHERE x = y'));

        $deleteIgnore->from(new TableIdentifier('foo', 'sch'))
            ->where('x = y');

        $deleteIgnore->prepareStatement($mockAdapter, $mockStatement);
    }

    /**
     * @coversNothing
     */
    public function testSpecificationconstantsCouldBeOverridedByExtensionInGetSqlString()
    {
        $deleteIgnore = new DeleteIgnore();

        $deleteIgnore->from('foo')
            ->where('x = y');
        self::assertEquals('DELETE IGNORE FROM "foo" WHERE x = y', $deleteIgnore->getSqlString());

        // with TableIdentifier
        $deleteIgnore = new DeleteIgnore();
        $deleteIgnore->from(new TableIdentifier('foo', 'sch'))
            ->where('x = y');
        self::assertEquals('DELETE IGNORE FROM "sch"."foo" WHERE x = y', $deleteIgnore->getSqlString());
    }
}
