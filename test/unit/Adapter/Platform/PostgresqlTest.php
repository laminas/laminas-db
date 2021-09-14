<?php

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Platform\Postgresql;
use PHPUnit\Framework\TestCase;

class PostgresqlTest extends TestCase
{
    /** @var Postgresql */
    protected $platform;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->platform = new Postgresql();
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::getName
     */
    public function testGetName()
    {
        self::assertEquals('PostgreSQL', $this->platform->getName());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::getQuoteIdentifierSymbol
     */
    public function testGetQuoteIdentifierSymbol()
    {
        self::assertEquals('"', $this->platform->getQuoteIdentifierSymbol());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::quoteIdentifier
     */
    public function testQuoteIdentifier()
    {
        self::assertEquals('"identifier"', $this->platform->quoteIdentifier('identifier'));
        self::assertEquals(
            '"identifier ""with"" double-quotes"',
            $this->platform->quoteIdentifier('identifier "with" double-quotes')
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::quoteIdentifierChain
     */
    public function testQuoteIdentifierChain()
    {
        self::assertEquals('"identifier"', $this->platform->quoteIdentifierChain('identifier'));
        self::assertEquals('"identifier"', $this->platform->quoteIdentifierChain(['identifier']));
        self::assertEquals('"schema"."identifier"', $this->platform->quoteIdentifierChain(['schema', 'identifier']));
        self::assertEquals(
            '"schema"."identifier ""with"" double-quotes"',
            $this->platform->quoteIdentifierChain(['schema', 'identifier "with" double-quotes'])
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::getQuoteValueSymbol
     */
    public function testGetQuoteValueSymbol()
    {
        self::assertEquals("'", $this->platform->getQuoteValueSymbol());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::quoteValue
     */
    public function testQuoteValueRaisesNoticeWithoutPlatformSupport()
    {
        $this->expectNotice();
        $this->expectNoticeMessage(
            'Attempting to quote a value in Laminas\Db\Adapter\Platform\Postgresql without extension/driver'
            . ' support can introduce security vulnerabilities in a production environment'
        );
        $this->platform->quoteValue('value');
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::quoteValue
     */
    public function testQuoteValue()
    {
        self::assertEquals("E'value'", @$this->platform->quoteValue('value'));
        self::assertEquals("E'Foo O\\'Bar'", @$this->platform->quoteValue("Foo O'Bar"));
        self::assertEquals(
            'E\'\\\'; DELETE FROM some_table; -- \'',
            @$this->platform->quoteValue('\'; DELETE FROM some_table; -- ')
        );
        self::assertEquals(
            "E'\\\\\\'; DELETE FROM some_table; -- '",
            @$this->platform->quoteValue('\\\'; DELETE FROM some_table; -- ')
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::quoteTrustedValue
     */
    public function testQuoteTrustedValue()
    {
        self::assertEquals("E'value'", $this->platform->quoteTrustedValue('value'));
        self::assertEquals("E'Foo O\\'Bar'", $this->platform->quoteTrustedValue("Foo O'Bar"));
        self::assertEquals(
            'E\'\\\'; DELETE FROM some_table; -- \'',
            $this->platform->quoteTrustedValue('\'; DELETE FROM some_table; -- ')
        );

        //                   '\\\'; DELETE FROM some_table; -- '  <- actual below
        self::assertEquals(
            "E'\\\\\\'; DELETE FROM some_table; -- '",
            $this->platform->quoteTrustedValue('\\\'; DELETE FROM some_table; -- ')
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::quoteValueList
     */
    public function testQuoteValueList()
    {
        $this->expectError();
        $this->expectErrorMessage(
            'Attempting to quote a value in Laminas\Db\Adapter\Platform\Postgresql without extension/driver'
            . ' support can introduce security vulnerabilities in a production environment'
        );
        self::assertEquals("'Foo O\'\'Bar'", $this->platform->quoteValueList("Foo O'Bar"));
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::getIdentifierSeparator
     */
    public function testGetIdentifierSeparator()
    {
        self::assertEquals('.', $this->platform->getIdentifierSeparator());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Postgresql::quoteIdentifierInFragment
     */
    public function testQuoteIdentifierInFragment()
    {
        self::assertEquals('"foo"."bar"', $this->platform->quoteIdentifierInFragment('foo.bar'));
        self::assertEquals('"foo" as "bar"', $this->platform->quoteIdentifierInFragment('foo as bar'));

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
