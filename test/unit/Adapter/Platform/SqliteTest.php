<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Platform\Sqlite;
use PHPUnit\Framework\Error;
use PHPUnit\Framework\TestCase;

class SqliteTest extends TestCase
{
    /**
     * @var Sqlite
     */
    protected $platform;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->platform = new Sqlite;
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::getName
     */
    public function testGetName()
    {
        self::assertEquals('SQLite', $this->platform->getName());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::getQuoteIdentifierSymbol
     */
    public function testGetQuoteIdentifierSymbol()
    {
        self::assertEquals('"', $this->platform->getQuoteIdentifierSymbol());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::quoteIdentifier
     */
    public function testQuoteIdentifier()
    {
        self::assertEquals('"identifier"', $this->platform->quoteIdentifier('identifier'));
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::quoteIdentifierChain
     */
    public function testQuoteIdentifierChain()
    {
        self::assertEquals('"identifier"', $this->platform->quoteIdentifierChain('identifier'));
        self::assertEquals('"identifier"', $this->platform->quoteIdentifierChain(['identifier']));
        self::assertEquals('"schema"."identifier"', $this->platform->quoteIdentifierChain(['schema', 'identifier']));
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::getQuoteValueSymbol
     */
    public function testGetQuoteValueSymbol()
    {
        self::assertEquals("'", $this->platform->getQuoteValueSymbol());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::quoteValue
     */
    public function testQuoteValueRaisesNoticeWithoutPlatformSupport()
    {
        $this->expectException(Error\Notice::class);
        $this->expectExceptionMessage(
            'Attempting to quote a value in Laminas\Db\Adapter\Platform\Sqlite without extension/driver support can '
            . 'introduce security vulnerabilities in a production environment'
        );
        $this->platform->quoteValue('value');
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::quoteValue
     */
    public function testQuoteValue()
    {
        self::assertEquals("'value'", @$this->platform->quoteValue('value'));
        self::assertEquals("'Foo O\\'Bar'", @$this->platform->quoteValue("Foo O'Bar"));
        self::assertEquals(
            '\'\\\'; DELETE FROM some_table; -- \'',
            @$this->platform->quoteValue('\'; DELETE FROM some_table; -- ')
        );
        self::assertEquals(
            "'\\\\\\'; DELETE FROM some_table; -- '",
            @$this->platform->quoteValue('\\\'; DELETE FROM some_table; -- ')
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::quoteTrustedValue
     */
    public function testQuoteTrustedValue()
    {
        self::assertEquals("'value'", $this->platform->quoteTrustedValue('value'));
        self::assertEquals("'Foo O\\'Bar'", $this->platform->quoteTrustedValue("Foo O'Bar"));
        self::assertEquals(
            '\'\\\'; DELETE FROM some_table; -- \'',
            $this->platform->quoteTrustedValue('\'; DELETE FROM some_table; -- ')
        );

        //                   '\\\'; DELETE FROM some_table; -- '  <- actual below
        self::assertEquals(
            "'\\\\\\'; DELETE FROM some_table; -- '",
            $this->platform->quoteTrustedValue('\\\'; DELETE FROM some_table; -- ')
        );
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::quoteValueList
     */
    public function testQuoteValueList()
    {
        $this->expectException(Error\Error::class);
        $this->expectExceptionMessage(
            'Attempting to quote a value in Laminas\Db\Adapter\Platform\Sqlite without extension/driver support can '
            . 'introduce security vulnerabilities in a production environment'
        );
        self::assertEquals("'Foo O\\'Bar'", $this->platform->quoteValueList("Foo O'Bar"));
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::getIdentifierSeparator
     */
    public function testGetIdentifierSeparator()
    {
        self::assertEquals('.', $this->platform->getIdentifierSeparator());
    }

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::quoteIdentifierInFragment
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

    /**
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::quoteValue
     * @covers \Laminas\Db\Adapter\Platform\Sqlite::quoteTrustedValue
     */
    public function testCanCloseConnectionAfterQuoteValue()
    {
        // Creating the SQLite database file
        $filePath = realpath(__DIR__) . "/_files/sqlite.db";
        if (! file_exists($filePath)) {
            touch($filePath);
        }

        $driver = new \Laminas\Db\Adapter\Driver\Pdo\Pdo([
            'driver' => 'Pdo_Sqlite',
            'database' => $filePath,
        ]);

        $this->platform->setDriver($driver);

        $this->platform->quoteValue("some; random]/ value");
        $this->platform->quoteTrustedValue("some; random]/ value");

        // Closing the connection so we can delete the file
        $driver->getConnection()->disconnect();

        @unlink($filePath);

        self::assertFileNotExists($filePath);
    }
}
