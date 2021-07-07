<?php

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\IsNull;
use Laminas\Db\Sql\Predicate\PredicateSet;
use LaminasTest\Db\DeprecatedAssertionsTrait;
use PHPUnit\Framework\TestCase;

class PredicateSetTest extends TestCase
{
    use DeprecatedAssertionsTrait;

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
        $set = new PredicateSet([
            new IsNull('foo'),
            new IsNull('bar'),
        ], 'OR');
        $parts = $set->getExpressionData();
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
        $predicateSet->addPredicates([new \Laminas\Db\Sql\Predicate\IsNotNull('c3')]);

        $predicates = $this->readAttribute($predicateSet, 'predicates');
        self::assertEquals('AND', $predicates[0][0]);
        self::assertInstanceOf('Laminas\Db\Sql\Predicate\Literal', $predicates[0][1]);

        self::assertEquals('AND', $predicates[1][0]);
        self::assertInstanceOf('Laminas\Db\Sql\Predicate\Expression', $predicates[1][1]);

        self::assertEquals('AND', $predicates[2][0]);
        self::assertInstanceOf('Laminas\Db\Sql\Predicate\Operator', $predicates[2][1]);

        self::assertEquals('OR', $predicates[3][0]);
        self::assertInstanceOf('Laminas\Db\Sql\Predicate\Literal', $predicates[3][1]);

        self::assertEquals('AND', $predicates[4][0]);
        self::assertInstanceOf('Laminas\Db\Sql\Predicate\IsNull', $predicates[4][1]);

        self::assertEquals('AND', $predicates[5][0]);
        self::assertInstanceOf('Laminas\Db\Sql\Predicate\In', $predicates[5][1]);

        self::assertEquals('AND', $predicates[6][0]);
        self::assertInstanceOf('Laminas\Db\Sql\Predicate\IsNotNull', $predicates[6][1]);

        $predicateSet->addPredicates(function ($what) use ($predicateSet) {
            self::assertSame($predicateSet, $what);
        });

        $this->expectException('Laminas\Db\Sql\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Predicate cannot be null');
        $predicateSet->addPredicates(null);
    }
}
