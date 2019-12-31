<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\NotLike;

class NotLikeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructEmptyArgs()
    {
        $notLike = new NotLike();
        $this->assertEquals('', $notLike->getIdentifier());
        $this->assertEquals('', $notLike->getLike());
    }

    public function testConstructWithArgs()
    {
        $notLike = new NotLike('bar', 'Foo%');
        $this->assertEquals('bar', $notLike->getIdentifier());
        $this->assertEquals('Foo%', $notLike->getLike());
    }

    public function testAccessorsMutators()
    {
        $notLike = new NotLike();
        $notLike->setIdentifier('bar');
        $this->assertEquals('bar', $notLike->getIdentifier());
        $notLike->setLike('foo%');
        $this->assertEquals('foo%', $notLike->getLike());
        $notLike->setSpecification('target = target');
        $this->assertEquals('target = target', $notLike->getSpecification());
    }

    public function testGetExpressionData()
    {
        $notLike = new NotLike('bar', 'Foo%');
        $this->assertEquals(
            array(
                array(
                    '%1$s NOT LIKE %2$s',
                    array('bar', 'Foo%'),
                    array($notLike::TYPE_IDENTIFIER, $notLike::TYPE_VALUE)
                )
            ),
            $notLike->getExpressionData()
        );
    }

    public function testInstanceOfPerSetters()
    {
        $notLike = new NotLike();
        $this->assertInstanceOf('Laminas\Db\Sql\Predicate\Like', $notLike->setIdentifier('bar'));
        $this->assertInstanceOf('Laminas\Db\Sql\Predicate\Like', $notLike->setSpecification('%1$s NOT LIKE %2$s'));
        $this->assertInstanceOf('Laminas\Db\Sql\Predicate\Like', $notLike->setLike('foo%'));
    }
}
