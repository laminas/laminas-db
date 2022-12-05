<?php

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Adapter\Platform\Sql92;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Db\Sql\Select;
use Laminas\Stdlib\ErrorHandler;
use PHPUnit\Framework\TestCase;

use const E_USER_NOTICE;

class PredicateTest extends TestCase
{
    public function testEqualToCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->equalTo('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%s = %s', $parts[0]);
        self::assertContains(['foo.bar', 'bar'], $parts[0]);
    }

    public function testNotEqualToCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->notEqualTo('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%s != %s', $parts[0]);
        self::assertContains(['foo.bar', 'bar'], $parts[0]);
    }

    public function testLessThanCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->lessThan('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%s < %s', $parts[0]);
        self::assertContains(['foo.bar', 'bar'], $parts[0]);
    }

    public function testGreaterThanCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->greaterThan('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%s > %s', $parts[0]);
        self::assertContains(['foo.bar', 'bar'], $parts[0]);
    }

    public function testLessThanOrEqualToCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->lessThanOrEqualTo('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%s <= %s', $parts[0]);
        self::assertContains(['foo.bar', 'bar'], $parts[0]);
    }

    public function testGreaterThanOrEqualToCreatesOperatorPredicate()
    {
        $predicate = new Predicate();
        $predicate->greaterThanOrEqualTo('foo.bar', 'bar');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%s >= %s', $parts[0]);
        self::assertContains(['foo.bar', 'bar'], $parts[0]);
    }

    public function testLikeCreatesLikePredicate()
    {
        $predicate = new Predicate();
        $predicate->like('foo.bar', 'bar%');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%1$s LIKE %2$s', $parts[0]);
        self::assertContains(['foo.bar', 'bar%'], $parts[0]);
    }

    public function testNotLikeCreatesLikePredicate()
    {
        $predicate = new Predicate();
        $predicate->notLike('foo.bar', 'bar%');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%1$s NOT LIKE %2$s', $parts[0]);
        self::assertContains(['foo.bar', 'bar%'], $parts[0]);
    }

    public function testLiteralCreatesLiteralPredicate()
    {
        $predicate = new Predicate();
        $predicate->literal('foo.bar = ?', 'bar');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('foo.bar = %s', $parts[0]);
        self::assertContains(['bar'], $parts[0]);
    }

    public function testIsNullCreatesIsNullPredicate()
    {
        $predicate = new Predicate();
        $predicate->isNull('foo.bar');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%1$s IS NULL', $parts[0]);
        self::assertContains(['foo.bar'], $parts[0]);
    }

    public function testIsNotNullCreatesIsNotNullPredicate()
    {
        $predicate = new Predicate();
        $predicate->isNotNull('foo.bar');
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%1$s IS NOT NULL', $parts[0]);
        self::assertContains(['foo.bar'], $parts[0]);
    }

    public function testInCreatesInPredicate()
    {
        $predicate = new Predicate();
        $predicate->in('foo.bar', ['foo', 'bar']);
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%s IN (%s, %s)', $parts[0]);
        self::assertContains(['foo.bar', 'foo', 'bar'], $parts[0]);
    }

    public function testNotInCreatesNotInPredicate()
    {
        $predicate = new Predicate();
        $predicate->notIn('foo.bar', ['foo', 'bar']);
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%s NOT IN (%s, %s)', $parts[0]);
        self::assertContains(['foo.bar', 'foo', 'bar'], $parts[0]);
    }

    public function testBetweenCreatesBetweenPredicate()
    {
        $predicate = new Predicate();
        $predicate->between('foo.bar', 1, 10);
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%1$s BETWEEN %2$s AND %3$s', $parts[0]);
        self::assertContains(['foo.bar', 1, 10], $parts[0]);
    }

    public function testBetweenCreatesNotBetweenPredicate()
    {
        $predicate = new Predicate();
        $predicate->notBetween('foo.bar', 1, 10);
        $parts = $predicate->getExpressionData();
        self::assertCount(1, $parts);
        self::assertContains('%1$s NOT BETWEEN %2$s AND %3$s', $parts[0]);
        self::assertContains(['foo.bar', 1, 10], $parts[0]);
    }

    public function testCanChainPredicateFactoriesBetweenOperators()
    {
        $predicate = new Predicate();
        $predicate->isNull('foo.bar')
                  ->or
                  ->isNotNull('bar.baz')
                  ->and
                  ->equalTo('baz.bat', 'foo');
        $parts = $predicate->getExpressionData();
        self::assertCount(5, $parts);

        self::assertContains('%1$s IS NULL', $parts[0]);
        self::assertContains(['foo.bar'], $parts[0]);

        self::assertEquals(' OR ', $parts[1]);

        self::assertContains('%1$s IS NOT NULL', $parts[2]);
        self::assertContains(['bar.baz'], $parts[2]);

        self::assertEquals(' AND ', $parts[3]);

        self::assertContains('%s = %s', $parts[4]);
        self::assertContains(['baz.bat', 'foo'], $parts[4]);
    }

    public function testCanNestPredicates()
    {
        $predicate = new Predicate();
        $predicate->isNull('foo.bar')
                  ->nest()
                  ->isNotNull('bar.baz')
                  ->and
                  ->equalTo('baz.bat', 'foo')
                  ->unnest();
        $parts = $predicate->getExpressionData();

        self::assertCount(7, $parts);

        self::assertContains('%1$s IS NULL', $parts[0]);
        self::assertContains(['foo.bar'], $parts[0]);

        self::assertEquals(' AND ', $parts[1]);

        self::assertEquals('(', $parts[2]);

        self::assertContains('%1$s IS NOT NULL', $parts[3]);
        self::assertContains(['bar.baz'], $parts[3]);

        self::assertEquals(' AND ', $parts[4]);

        self::assertContains('%s = %s', $parts[5]);
        self::assertContains(['baz.bat', 'foo'], $parts[5]);

        self::assertEquals(')', $parts[6]);
    }

    /**
     * @testdox Unit test: Test expression() is chainable and returns proper values
     */
    public function testExpression()
    {
        $predicate = new Predicate();

        // is chainable
        self::assertSame($predicate, $predicate->expression('foo = ?', 0));
        // with parameter
        self::assertEquals(
            [['foo = %s', [0], [Expression::TYPE_VALUE]]],
            $predicate->getExpressionData()
        );
    }

    /**
     * @testdox Unit test: Test expression() allows null $parameters
     */
    public function testExpressionNullParameters()
    {
        $predicate = new Predicate();

        $predicate->expression('foo = bar');
        $predicates = $predicate->getPredicates();
        $expression = $predicates[0][1];
        self::assertEquals([], $expression->getParameters());
    }

    /**
     * @testdox Unit test: Test literal() is chainable, returns proper values, and is backwards compatible with 2.0.*
     */
    public function testLiteral()
    {
        $predicate = new Predicate();

        // is chainable
        self::assertSame($predicate, $predicate->literal('foo = bar'));
        // with parameter
        self::assertEquals(
            [['foo = bar', [], []]],
            $predicate->getExpressionData()
        );

        // test literal() is backwards-compatible, and works with with parameters
        $predicate = new Predicate();
        $predicate->expression('foo = ?', 'bar');
        // with parameter
        self::assertEquals(
            [['foo = %s', ['bar'], [Expression::TYPE_VALUE]]],
            $predicate->getExpressionData()
        );

        // test literal() is backwards-compatible, and works with with parameters, even 0 which tests as false
        $predicate = new Predicate();
        $predicate->expression('foo = ?', 0);
        // with parameter
        self::assertEquals(
            [['foo = %s', [0], [Expression::TYPE_VALUE]]],
            $predicate->getExpressionData()
        );
    }

    public function testCanCreateExpressionsWithoutAnyBoundSqlParameters(): void
    {
        $where1 = new Predicate();

        $where1->expression('some_expression()');

        self::assertSame(
            'SELECT "a_table".* FROM "a_table" WHERE (some_expression())',
            $this->makeSqlString($where1)
        );
    }

    public function testWillBindSqlParametersToExpressionsWithGivenParameter(): void
    {
        $where = new Predicate();

        $where->expression('some_expression(?)', null);

        self::assertSame(
            'SELECT "a_table".* FROM "a_table" WHERE (some_expression(\'\'))',
            $this->makeSqlString($where)
        );
    }

    public function testWillBindSqlParametersToExpressionsWithGivenStringParameter(): void
    {
        $where = new Predicate();

        $where->expression('some_expression(?)', 'a string');

        self::assertSame(
            'SELECT "a_table".* FROM "a_table" WHERE (some_expression(\'a string\'))',
            $this->makeSqlString($where)
        );
    }

    private function makeSqlString(Predicate $where): string
    {
        $select = new Select('a_table');

        $select->where($where);

        // this is still faster than connecting to a real DB for this kind of test.
        // we are using unsafe SQL quoting on purpose here: this raises warnings in production.
        ErrorHandler::start(E_USER_NOTICE);

        try {
            $string = $select->getSqlString(new Sql92());
        } finally {
            ErrorHandler::stop();
        }

        return $string;
    }
}
