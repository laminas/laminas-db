<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Db\Sql\Predicate\In;
use Laminas\Db\Sql\Predicate\IsNotNull;
use Laminas\Db\Sql\Predicate\IsNull;
use Laminas\Db\Sql\Predicate\Literal;
use Laminas\Db\Sql\Predicate\Operator;
use Laminas\Db\Sql\Predicate\PredicateSet;
use PHPUnit\Framework\TestCase;

use function var_export;

class PredicateSetTest extends TestCase
{
    public function testEmptyConstructorYieldsCountOfZero()
    {
        $predicateSet = new PredicateSet();
        self::assertCount(0, $predicateSet);
    }

    public function testCombinationIsAndByDefault()
    {
        $predicateSet = new PredicateSet();
        $predicateSet->addPredicate(new IsNull('foo'))
                  ->addPredicate(new IsNull('bar'));
        $parts = $predicateSet->getExpressionData();
        self::assertCount(3, $parts);
        self::assertStringContainsString('AND', $parts[1]);
        self::assertStringNotContainsString('OR', $parts[1]);
    }

    public function testCanPassPredicatesAndDefaultCombinationViaConstructor()
    {
        $predicateSet = new PredicateSet();
        $set          = new PredicateSet([
            new IsNull('foo'),
            new IsNull('bar'),
        ], 'OR');
        $parts        = $set->getExpressionData();
        self::assertCount(3, $parts);
        self::assertStringContainsString('OR', $parts[1]);
        self::assertStringNotContainsString('AND', $parts[1]);
    }

    public function testCanPassBothPredicateAndCombinationToAddPredicate()
    {
        $predicateSet = new PredicateSet();
        $predicateSet->addPredicate(new IsNull('foo'), 'OR')
                  ->addPredicate(new IsNull('bar'), 'AND')
                  ->addPredicate(new IsNull('baz'), 'OR')
                  ->addPredicate(new IsNull('bat'), 'AND');
        $parts = $predicateSet->getExpressionData();
        self::assertCount(7, $parts);

        self::assertStringNotContainsString('OR', $parts[1], var_export($parts, 1));
        self::assertStringContainsString('AND', $parts[1]);

        self::assertStringContainsString('OR', $parts[3]);
        self::assertStringNotContainsString('AND', $parts[3]);

        self::assertStringNotContainsString('OR', $parts[5]);
        self::assertStringContainsString('AND', $parts[5]);
    }

    public function testCanUseOrPredicateAndAndPredicateMethods()
    {
        $predicateSet = new PredicateSet();
        $predicateSet->orPredicate(new IsNull('foo'))
                  ->andPredicate(new IsNull('bar'))
                  ->orPredicate(new IsNull('baz'))
                  ->andPredicate(new IsNull('bat'));
        $parts = $predicateSet->getExpressionData();
        self::assertCount(7, $parts);

        self::assertStringNotContainsString('OR', $parts[1], var_export($parts, 1));
        self::assertStringContainsString('AND', $parts[1]);

        self::assertStringContainsString('OR', $parts[3]);
        self::assertStringNotContainsString('AND', $parts[3]);

        self::assertStringNotContainsString('OR', $parts[5]);
        self::assertStringContainsString('AND', $parts[5]);
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\PredicateSet::addPredicates
     */
    public function testAddPredicates()
    {
        $predicateSet = new PredicateSet();

        $predicateSet->addPredicates('x = y');
        $predicateSet->addPredicates(['foo > ?' => 5]);
        $predicateSet->addPredicates(['id' => 2]);
        $predicateSet->addPredicates(['a = b'], PredicateSet::OP_OR);
        $predicateSet->addPredicates(['c1' => null]);
        $predicateSet->addPredicates(['c2' => [1, 2, 3]]);
        $predicateSet->addPredicates([new IsNotNull('c3')]);

        $predicates = (function ($predicateSet) {
            return $predicateSet->predicates;
        })->bindTo($predicateSet, $predicateSet)($predicateSet);
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

        $predicateSet->addPredicates(function ($what) use ($predicateSet) {
            self::assertSame($predicateSet, $what);
        });

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Predicate cannot be null');
        $predicateSet->addPredicates(null);
    }
}
