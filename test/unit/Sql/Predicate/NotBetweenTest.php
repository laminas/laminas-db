<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\NotBetween;
use PHPUnit\Framework\TestCase;

class NotBetweenTest extends TestCase
{
    /**
     * @var NotBetween
     */
    protected $notBetween;

    protected function setUp()
    {
        $this->notBetween = new NotBetween();
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\NotBetween::getSpecification
     */
    public function testSpecificationHasSameDefaultValue()
    {
        self::assertEquals('%1$s NOT BETWEEN %2$s AND %3$s', $this->notBetween->getSpecification());
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\NotBetween::getExpressionData
     */
    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $this->notBetween->setIdentifier('foo.bar')
                      ->setMinValue(10)
                      ->setMaxValue(19);
        $expected = [[
            $this->notBetween->getSpecification(),
            ['foo.bar', 10, 19],
            [NotBetween::TYPE_IDENTIFIER, NotBetween::TYPE_VALUE, NotBetween::TYPE_VALUE],
        ]];
        self::assertEquals($expected, $this->notBetween->getExpressionData());

        $this->notBetween->setIdentifier([10 => NotBetween::TYPE_VALUE])
                      ->setMinValue(['foo.bar' => NotBetween::TYPE_IDENTIFIER])
                      ->setMaxValue(['foo.baz' => NotBetween::TYPE_IDENTIFIER]);
        $expected = [[
            $this->notBetween->getSpecification(),
            [10, 'foo.bar', 'foo.baz'],
            [NotBetween::TYPE_VALUE, NotBetween::TYPE_IDENTIFIER, NotBetween::TYPE_IDENTIFIER],
        ]];
        self::assertEquals($expected, $this->notBetween->getExpressionData());
    }
}
