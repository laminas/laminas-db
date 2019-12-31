<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\Like;
use PHPUnit\Framework\TestCase;

class LikeTest extends TestCase
{
    public function testConstructEmptyArgs()
    {
        $like = new Like();
        self::assertEquals('', $like->getIdentifier());
        self::assertEquals('', $like->getLike());
    }

    public function testConstructWithArgs()
    {
        $like = new Like('bar', 'Foo%');
        self::assertEquals('bar', $like->getIdentifier());
        self::assertEquals('Foo%', $like->getLike());
    }

    public function testAccessorsMutators()
    {
        $like = new Like();
        $like->setIdentifier('bar');
        self::assertEquals('bar', $like->getIdentifier());
        $like->setLike('foo%');
        self::assertEquals('foo%', $like->getLike());
        $like->setSpecification('target = target');
        self::assertEquals('target = target', $like->getSpecification());
    }

    public function testGetExpressionData()
    {
        $like = new Like('bar', 'Foo%');
        self::assertEquals(
            [
                ['%1$s LIKE %2$s', ['bar', 'Foo%'], [$like::TYPE_IDENTIFIER, $like::TYPE_VALUE]],
            ],
            $like->getExpressionData()
        );

        $like = new Like(['Foo%' => $like::TYPE_VALUE], ['bar' => $like::TYPE_IDENTIFIER]);
        self::assertEquals(
            [
                ['%1$s LIKE %2$s', ['Foo%', 'bar'], [$like::TYPE_VALUE, $like::TYPE_IDENTIFIER]],
            ],
            $like->getExpressionData()
        );
    }

    public function testInstanceOfPerSetters()
    {
        $like = new Like();
        self::assertInstanceOf('Laminas\Db\Sql\Predicate\Like', $like->setIdentifier('bar'));
        self::assertInstanceOf('Laminas\Db\Sql\Predicate\Like', $like->setSpecification('%1$s LIKE %2$s'));
        self::assertInstanceOf('Laminas\Db\Sql\Predicate\Like', $like->setLike('foo%'));
    }
}
