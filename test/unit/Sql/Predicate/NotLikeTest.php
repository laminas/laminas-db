<?php

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\Like;
use Laminas\Db\Sql\Predicate\NotLike;
use PHPUnit\Framework\TestCase;

class NotLikeTest extends TestCase
{
    public function testConstructEmptyArgs()
    {
        $notLike = new NotLike();
        self::assertEquals('', $notLike->getIdentifier());
        self::assertEquals('', $notLike->getLike());
    }

    public function testConstructWithArgs()
    {
        $notLike = new NotLike('bar', 'Foo%');
        self::assertEquals('bar', $notLike->getIdentifier());
        self::assertEquals('Foo%', $notLike->getLike());
    }

    public function testAccessorsMutators()
    {
        $notLike = new NotLike();
        $notLike->setIdentifier('bar');
        self::assertEquals('bar', $notLike->getIdentifier());
        $notLike->setLike('foo%');
        self::assertEquals('foo%', $notLike->getLike());
        $notLike->setSpecification('target = target');
        self::assertEquals('target = target', $notLike->getSpecification());
    }

    public function testGetExpressionData()
    {
        $notLike = new NotLike('bar', 'Foo%');
        self::assertEquals(
            [
                [
                    '%1$s NOT LIKE %2$s',
                    ['bar', 'Foo%'],
                    [$notLike::TYPE_IDENTIFIER, $notLike::TYPE_VALUE],
                ],
            ],
            $notLike->getExpressionData()
        );
    }

    public function testInstanceOfPerSetters()
    {
        $notLike = new NotLike();
        self::assertInstanceOf(Like::class, $notLike->setIdentifier('bar'));
        self::assertInstanceOf(Like::class, $notLike->setSpecification('%1$s NOT LIKE %2$s'));
        self::assertInstanceOf(Like::class, $notLike->setLike('foo%'));
    }
}
