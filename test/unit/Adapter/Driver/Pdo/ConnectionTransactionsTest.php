<?php

namespace LaminasTest\Db\Adapter\Driver\Pdo;

use Laminas\Db\Adapter\Driver\Pdo\Connection;
use Laminas\Db\Adapter\Exception\RuntimeException;
use LaminasTest\Db\TestAsset\ConnectionWrapper;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see \Laminas\Db\Adapter\Driver\Pdo\Connection} transaction support
 *
 * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection
 * @covers \Laminas\Db\Adapter\Driver\AbstractConnection
 */
class ConnectionTransactionsTest extends TestCase
{
    /** @var Wrapper */
    protected $wrapper;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->wrapper = new ConnectionWrapper();
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::beginTransaction()
     */
    public function testBeginTransactionReturnsInstanceOfConnection()
    {
        self::assertInstanceOf(Connection::class, $this->wrapper->beginTransaction());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::beginTransaction()
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     */
    public function testBeginTransactionSetsInTransactionAtTrue()
    {
        $this->wrapper->beginTransaction();
        self::assertTrue($this->wrapper->inTransaction());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::commit()
     */
    public function testCommitReturnsInstanceOfConnection()
    {
        $this->wrapper->beginTransaction();
        self::assertInstanceOf(Connection::class, $this->wrapper->commit());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::commit()
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     */
    public function testCommitSetsInTransactionAtFalse()
    {
        $this->wrapper->beginTransaction();
        $this->wrapper->commit();
        self::assertFalse($this->wrapper->inTransaction());
    }

    /**
     * Standalone commit after a SET autocommit=0;
     *
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::commit()
     */
    public function testCommitWithoutBeginReturnsInstanceOfConnection()
    {
        self::assertInstanceOf(Connection::class, $this->wrapper->commit());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::beginTransaction()
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::commit()
     */
    public function testNestedTransactionsCommit()
    {
        $nested = 0;

        self::assertFalse($this->wrapper->inTransaction());

        // 1st transaction
        $this->wrapper->beginTransaction();
        self::assertTrue($this->wrapper->inTransaction());
        self::assertSame(++$nested, $this->wrapper->getNestedTransactionsCount());

        // 2nd transaction
        $this->wrapper->beginTransaction();
        self::assertTrue($this->wrapper->inTransaction());
        self::assertSame(++$nested, $this->wrapper->getNestedTransactionsCount());

        // 1st commit
        $this->wrapper->commit();
        self::assertTrue($this->wrapper->inTransaction());
        self::assertSame(--$nested, $this->wrapper->getNestedTransactionsCount());

        // 2nd commit
        $this->wrapper->commit();
        self::assertFalse($this->wrapper->inTransaction());
        self::assertSame(--$nested, $this->wrapper->getNestedTransactionsCount());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::beginTransaction()
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::rollback()
     */
    public function testNestedTransactionsRollback()
    {
        $nested = 0;

        self::assertFalse($this->wrapper->inTransaction());

        // 1st transaction
        $this->wrapper->beginTransaction();
        self::assertTrue($this->wrapper->inTransaction());
        self::assertSame(++$nested, $this->wrapper->getNestedTransactionsCount());

        // 2nd transaction
        $this->wrapper->beginTransaction();
        self::assertTrue($this->wrapper->inTransaction());
        self::assertSame(++$nested, $this->wrapper->getNestedTransactionsCount());

        // Rollback
        $this->wrapper->rollback();
        self::assertFalse($this->wrapper->inTransaction());
        self::assertSame(0, $this->wrapper->getNestedTransactionsCount());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::rollback()
     */
    public function testRollbackDisconnectedThrowsException()
    {
        $this->wrapper->disconnect();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Must be connected before you can rollback');
        $this->wrapper->rollback();
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::rollback()
     */
    public function testRollbackReturnsInstanceOfConnection()
    {
        $this->wrapper->beginTransaction();
        self::assertInstanceOf(Connection::class, $this->wrapper->rollback());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::rollback()
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     */
    public function testRollbackSetsInTransactionAtFalse()
    {
        $this->wrapper->beginTransaction();
        $this->wrapper->rollback();
        self::assertFalse($this->wrapper->inTransaction());
    }

    /**
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::rollback()
     */
    public function testRollbackWithoutBeginThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Must call beginTransaction() before you can rollback');
        $this->wrapper->rollback();
    }

    /**
     * Standalone commit after a SET autocommit=0;
     *
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     * @covers \Laminas\Db\Adapter\Driver\Pdo\Connection::commit()
     */
    public function testStandaloneCommit()
    {
        self::assertFalse($this->wrapper->inTransaction());
        self::assertSame(0, $this->wrapper->getNestedTransactionsCount());

        $this->wrapper->commit();

        self::assertFalse($this->wrapper->inTransaction());
        self::assertSame(0, $this->wrapper->getNestedTransactionsCount());
    }
}
