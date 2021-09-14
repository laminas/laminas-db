<?php

namespace LaminasTest\Db\Sql\Predicate;

use Laminas\Db\Sql\Predicate\Between;
use PHPUnit\Framework\TestCase;

class BetweenTest extends TestCase
{
    /** @var Between */
    protected $between;

    protected function setUp(): void
    {
        $this->between = new Between();
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\Between::__construct
     * @covers \Laminas\Db\Sql\Predicate\Between::getIdentifier
     * @covers \Laminas\Db\Sql\Predicate\Between::getMinValue
     * @covers \Laminas\Db\Sql\Predicate\Between::getMaxValue
     */
    public function testConstructorYieldsNullIdentifierMinimumAndMaximumValues()
    {
        self::assertNull($this->between->getIdentifier());
        self::assertNull($this->between->getMinValue());
        self::assertNull($this->between->getMaxValue());
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\Between::__construct
     * @covers \Laminas\Db\Sql\Predicate\Between::getIdentifier
     * @covers \Laminas\Db\Sql\Predicate\Between::getMinValue
     * @covers \Laminas\Db\Sql\Predicate\Between::getMaxValue
     */
    public function testConstructorCanPassIdentifierMinimumAndMaximumValues()
    {
        $between = new Between('foo.bar', 1, 300);
        self::assertEquals('foo.bar', $between->getIdentifier());
        self::assertSame(1, $between->getMinValue());
        self::assertSame(300, $between->getMaxValue());

        $between = new Between('foo.bar', 0, 1);
        self::assertEquals('foo.bar', $between->getIdentifier());
        self::assertSame(0, $between->getMinValue());
        self::assertSame(1, $between->getMaxValue());

        $between = new Between('foo.bar', -1, 0);
        self::assertEquals('foo.bar', $between->getIdentifier());
        self::assertSame(-1, $between->getMinValue());
        self::assertSame(0, $between->getMaxValue());
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\Between::getSpecification
     */
    public function testSpecificationHasSaneDefaultValue()
    {
        self::assertEquals('%1$s BETWEEN %2$s AND %3$s', $this->between->getSpecification());
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\Between::setIdentifier
     * @covers \Laminas\Db\Sql\Predicate\Between::getIdentifier
     */
    public function testIdentifierIsMutable()
    {
        $this->between->setIdentifier('foo.bar');
        self::assertEquals('foo.bar', $this->between->getIdentifier());
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\Between::setMinValue
     * @covers \Laminas\Db\Sql\Predicate\Between::getMinValue
     */
    public function testMinValueIsMutable()
    {
        $this->between->setMinValue(10);
        self::assertEquals(10, $this->between->getMinValue());
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\Between::setMaxValue
     * @covers \Laminas\Db\Sql\Predicate\Between::getMaxValue
     */
    public function testMaxValueIsMutable()
    {
        $this->between->setMaxValue(10);
        self::assertEquals(10, $this->between->getMaxValue());
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\Between::setSpecification
     * @covers \Laminas\Db\Sql\Predicate\Between::getSpecification
     */
    public function testSpecificationIsMutable()
    {
        $this->between->setSpecification('%1$s IS INBETWEEN %2$s AND %3$s');
        self::assertEquals('%1$s IS INBETWEEN %2$s AND %3$s', $this->between->getSpecification());
    }

    /**
     * @covers \Laminas\Db\Sql\Predicate\Between::getExpressionData
     */
    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $this->between->setIdentifier('foo.bar')
                      ->setMinValue(10)
                      ->setMaxValue(19);
        $expected = [
            [
                $this->between->getSpecification(),
                ['foo.bar', 10, 19],
                [Between::TYPE_IDENTIFIER, Between::TYPE_VALUE, Between::TYPE_VALUE],
            ],
        ];
        self::assertEquals($expected, $this->between->getExpressionData());

        $this->between->setIdentifier([10 => Between::TYPE_VALUE])
                      ->setMinValue(['foo.bar' => Between::TYPE_IDENTIFIER])
                      ->setMaxValue(['foo.baz' => Between::TYPE_IDENTIFIER]);
        $expected = [
            [
                $this->between->getSpecification(),
                [10, 'foo.bar', 'foo.baz'],
                [Between::TYPE_VALUE, Between::TYPE_IDENTIFIER, Between::TYPE_IDENTIFIER],
            ],
        ];
        self::assertEquals($expected, $this->between->getExpressionData());
    }
}
