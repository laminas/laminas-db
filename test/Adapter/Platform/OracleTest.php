<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Platform\Oracle;

class OracleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Oracle
     */
    protected $platform;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->platform = new Oracle;
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Oracle::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Oracle', $this->platform->getName());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Oracle::getQuoteIdentifierSymbol
     */
    public function testGetQuoteIdentifierSymbol()
    {
        $this->assertEquals('"', $this->platform->getQuoteIdentifierSymbol());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Oracle::quoteIdentifier
     */
    public function testQuoteIdentifier()
    {
        $this->assertEquals('"identifier"', $this->platform->quoteIdentifier('identifier'));

        $platform = new Oracle(array('quote_identifiers' => false));
        $this->assertEquals('identifier', $platform->quoteIdentifier('identifier'));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Oracle::quoteIdentifierChain
     */
    public function testQuoteIdentifierChain()
    {
        $this->assertEquals('"identifier"', $this->platform->quoteIdentifierChain('identifier'));
        $this->assertEquals('"identifier"', $this->platform->quoteIdentifierChain(array('identifier')));
        $this->assertEquals('"schema"."identifier"', $this->platform->quoteIdentifierChain(array('schema','identifier')));

        $platform = new Oracle(array('quote_identifiers' => false));
        $this->assertEquals('identifier', $platform->quoteIdentifierChain('identifier'));
        $this->assertEquals('identifier', $platform->quoteIdentifierChain(array('identifier')));
        $this->assertEquals('schema.identifier', $platform->quoteIdentifierChain(array('schema','identifier')));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Oracle::getQuoteValueSymbol
     */
    public function testGetQuoteValueSymbol()
    {
        $this->assertEquals("'", $this->platform->getQuoteValueSymbol());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Oracle::quoteValue
     */
    public function testQuoteValue()
    {
        $this->assertEquals("'value'", $this->platform->quoteValue('value'));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Oracle::quoteValueList
     */
    public function testQuoteValueList()
    {
        $this->assertEquals("'Foo O\\'Bar'", $this->platform->quoteValueList("Foo O'Bar"));
        $this->assertEquals("'Foo O\\'Bar'", $this->platform->quoteValueList(array("Foo O'Bar")));
        $this->assertEquals("'value', 'Foo O\\'Bar'", $this->platform->quoteValueList(array('value',"Foo O'Bar")));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Oracle::getIdentifierSeparator
     */
    public function testGetIdentifierSeparator()
    {
        $this->assertEquals('.', $this->platform->getIdentifierSeparator());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Oracle::quoteIdentifierInFragment
     */
    public function testQuoteIdentifierInFragment()
    {
        $this->assertEquals('"foo"."bar"', $this->platform->quoteIdentifierInFragment('foo.bar'));
        $this->assertEquals('"foo" as "bar"', $this->platform->quoteIdentifierInFragment('foo as bar'));

        $platform = new Oracle(array('quote_identifiers' => false));
        $this->assertEquals('foo.bar', $platform->quoteIdentifierInFragment('foo.bar'));
        $this->assertEquals('foo as bar', $platform->quoteIdentifierInFragment('foo as bar'));

        // single char words
        $this->assertEquals('("foo"."bar" = "boo"."baz")', $this->platform->quoteIdentifierInFragment('(foo.bar = boo.baz)', array('(', ')', '=')));

        // case insensitive safe words
        $this->assertEquals(
            '("foo"."bar" = "boo"."baz") AND ("foo"."baz" = "boo"."baz")',
            $this->platform->quoteIdentifierInFragment('(foo.bar = boo.baz) AND (foo.baz = boo.baz)', array('(', ')', '=', 'and'))
        );
    }
}
