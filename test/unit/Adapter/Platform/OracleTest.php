<?php

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\Oci8\Oci8;
use Laminas\Db\Adapter\Exception\InvalidArgumentException;
use Laminas\Db\Adapter\Platform\Oracle;
use PHPUnit\Framework\TestCase;

class OracleTest extends TestCase
{
    /** @var Oracle */
    protected $platform;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->platform = new Oracle();
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::__construct
     */
    public function testContructWithOptions()
    {
        self::assertEquals('"\'test\'.\'test\'"', $this->platform->quoteIdentifier('"test"."test"'));
        $plataform1 = new Oracle(['quote_identifiers' => false]);
        self::assertEquals('"test"."test"', $plataform1->quoteIdentifier('"test"."test"'));
        $plataform2 = new Oracle(['quote_identifiers' => 'false']);
        self::assertEquals('"test"."test"', $plataform2->quoteIdentifier('"test"."test"'));
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::__construct
     */
    public function testContructWithDriver()
    {
        $mockDriver = $this->getMockForAbstractClass(
            Oci8::class,
            [[]],
            '',
            true,
            true,
            true,
            []
        );
        $platform   = new Oracle([], $mockDriver);
        self::assertEquals($mockDriver, $platform->getDriver());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::setDriver
     */
    public function testSetDriver()
    {
        $mockDriver = $this->getMockForAbstractClass(
            Oci8::class,
            [[]],
            '',
            true,
            true,
            true,
            []
        );
        $platform   = $this->platform->setDriver($mockDriver);
        self::assertEquals($mockDriver, $platform->getDriver());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::setDriver
     */
    public function testSetDriverInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            '$driver must be a Oci8 or Oracle PDO Laminas\Db\Adapter\Driver, Oci8 instance, or Oci PDO instance'
        );
        $this->platform->setDriver(null);
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::getDriver
     */
    public function testGetDriver()
    {
        self::assertNull($this->platform->getDriver());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::getName
     */
    public function testGetName()
    {
        self::assertEquals('Oracle', $this->platform->getName());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::getQuoteIdentifierSymbol
     */
    public function testGetQuoteIdentifierSymbol()
    {
        self::assertEquals('"', $this->platform->getQuoteIdentifierSymbol());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::quoteIdentifier
     */
    public function testQuoteIdentifier()
    {
        self::assertEquals('"identifier"', $this->platform->quoteIdentifier('identifier'));

        $platform = new Oracle(['quote_identifiers' => false]);
        self::assertEquals('identifier', $platform->quoteIdentifier('identifier'));
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::quoteIdentifierChain
     */
    public function testQuoteIdentifierChain()
    {
        self::assertEquals('"identifier"', $this->platform->quoteIdentifierChain('identifier'));
        self::assertEquals('"identifier"', $this->platform->quoteIdentifierChain(['identifier']));
        self::assertEquals('"schema"."identifier"', $this->platform->quoteIdentifierChain(['schema', 'identifier']));

        $platform = new Oracle(['quote_identifiers' => false]);
        self::assertEquals('identifier', $platform->quoteIdentifierChain('identifier'));
        self::assertEquals('identifier', $platform->quoteIdentifierChain(['identifier']));
        self::assertEquals('schema.identifier', $platform->quoteIdentifierChain(['schema', 'identifier']));
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::getQuoteValueSymbol
     */
    public function testGetQuoteValueSymbol()
    {
        self::assertEquals("'", $this->platform->getQuoteValueSymbol());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::quoteValue
     */
    public function testQuoteValueRaisesNoticeWithoutPlatformSupport()
    {
        $this->expectNotice();
        $this->expectNoticeMessage(
            'Attempting to quote a value in Laminas\Db\Adapter\Platform\Oracle without '
            . 'extension/driver support can introduce security vulnerabilities in a production environment'
        );
        $this->platform->quoteValue('value');
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::quoteValue
     */
    public function testQuoteValue()
    {
        self::assertEquals("'value'", @$this->platform->quoteValue('value'));
        self::assertEquals("'Foo O''Bar'", @$this->platform->quoteValue("Foo O'Bar"));
        self::assertEquals(
            '\'\'\'; DELETE FROM some_table; -- \'',
            @$this->platform->quoteValue('\'; DELETE FROM some_table; -- ')
        );
        self::assertEquals(
            "'\\''; DELETE FROM some_table; -- '",
            @$this->platform->quoteValue('\\\'; DELETE FROM some_table; -- ')
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::quoteTrustedValue
     */
    public function testQuoteTrustedValue()
    {
        self::assertEquals("'value'", $this->platform->quoteTrustedValue('value'));
        self::assertEquals("'Foo O''Bar'", $this->platform->quoteTrustedValue("Foo O'Bar"));
        self::assertEquals(
            '\'\'\'; DELETE FROM some_table; -- \'',
            $this->platform->quoteTrustedValue('\'; DELETE FROM some_table; -- ')
        );

        //                   '\\\'; DELETE FROM some_table; -- '  <- actual below
        self::assertEquals(
            "'\\''; DELETE FROM some_table; -- '",
            $this->platform->quoteTrustedValue('\\\'; DELETE FROM some_table; -- ')
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::quoteValueList
     */
    public function testQuoteValueList()
    {
        $this->expectError();
        $this->expectErrorMessage(
            'Attempting to quote a value in Laminas\Db\Adapter\Platform\Oracle without '
            . 'extension/driver support can introduce security vulnerabilities in a production environment'
        );
        self::assertEquals("'Foo O''Bar'", $this->platform->quoteValueList("Foo O'Bar"));
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::getIdentifierSeparator
     */
    public function testGetIdentifierSeparator()
    {
        self::assertEquals('.', $this->platform->getIdentifierSeparator());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Oracle::quoteIdentifierInFragment
     */
    public function testQuoteIdentifierInFragment()
    {
        self::assertEquals('"foo"."bar"', $this->platform->quoteIdentifierInFragment('foo.bar'));
        self::assertEquals('"foo" as "bar"', $this->platform->quoteIdentifierInFragment('foo as bar'));

        $platform = new Oracle(['quote_identifiers' => false]);
        self::assertEquals('foo.bar', $platform->quoteIdentifierInFragment('foo.bar'));
        self::assertEquals('foo as bar', $platform->quoteIdentifierInFragment('foo as bar'));

        // single char words
        self::assertEquals(
            '("foo"."bar" = "boo"."baz")',
            $this->platform->quoteIdentifierInFragment('(foo.bar = boo.baz)', ['(', ')', '='])
        );

        // case insensitive safe words
        self::assertEquals(
            '("foo"."bar" = "boo"."baz") AND ("foo"."baz" = "boo"."baz")',
            $this->platform->quoteIdentifierInFragment(
                '(foo.bar = boo.baz) AND (foo.baz = boo.baz)',
                ['(', ')', '=', 'and']
            )
        );

        // case insensitive safe words in field
        self::assertEquals(
            '("foo"."bar" = "boo".baz) AND ("foo".baz = "boo".baz)',
            $this->platform->quoteIdentifierInFragment(
                '(foo.bar = boo.baz) AND (foo.baz = boo.baz)',
                ['(', ')', '=', 'and', 'bAz']
            )
        );
    }
}
