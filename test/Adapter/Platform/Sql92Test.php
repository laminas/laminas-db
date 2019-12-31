<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Platform\Sql92;

class Sql92Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Sql92
     */
    protected $platform;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->platform = new Sql92;
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Sql92::getName
     */
    public function testGetName()
    {
        $this->assertEquals('SQL92', $this->platform->getName());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Sql92::getQuoteIdentifierSymbol
     */
    public function testGetQuoteIdentifierSymbol()
    {
        $this->assertEquals('"', $this->platform->getQuoteIdentifierSymbol());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Sql92::quoteIdentifier
     */
    public function testQuoteIdentifier()
    {
        $this->assertEquals('"identifier"', $this->platform->quoteIdentifier('identifier'));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Sql92::quoteIdentifierChain
     */
    public function testQuoteIdentifierChain()
    {
        $this->assertEquals('"identifier"', $this->platform->quoteIdentifierChain('identifier'));
        $this->assertEquals('"identifier"', $this->platform->quoteIdentifierChain(array('identifier')));
        $this->assertEquals('"schema"."identifier"', $this->platform->quoteIdentifierChain(array('schema','identifier')));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Sql92::getQuoteValueSymbol
     */
    public function testGetQuoteValueSymbol()
    {
        $this->assertEquals("'", $this->platform->getQuoteValueSymbol());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Sql92::quoteValue
     */
    public function testQuoteValue()
    {
        $this->assertEquals("'value'", $this->platform->quoteValue('value'));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Sql92::quoteValueList
     */
    public function testQuoteValueList()
    {
        $this->assertEquals("'Foo O\\'Bar'", $this->platform->quoteValueList("Foo O'Bar"));
        $this->assertEquals("'Foo O\\'Bar'", $this->platform->quoteValueList(array("Foo O'Bar")));
        $this->assertEquals("'value', 'Foo O\\'Bar'", $this->platform->quoteValueList(array('value',"Foo O'Bar")));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Sql92::getIdentifierSeparator
     */
    public function testGetIdentifierSeparator()
    {
        $this->assertEquals('.', $this->platform->getIdentifierSeparator());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\Sql92::quoteIdentifierInFragment
     */
    public function testQuoteIdentifierInFragment()
    {
        $this->assertEquals('"foo"."bar"', $this->platform->quoteIdentifierInFragment('foo.bar'));
        $this->assertEquals('"foo" as "bar"', $this->platform->quoteIdentifierInFragment('foo as bar'));

        // single char words
        $this->assertEquals('("foo"."bar" = "boo"."baz")', $this->platform->quoteIdentifierInFragment('(foo.bar = boo.baz)', array('(', ')', '=')));

        // case insensitive safe words
        $this->assertEquals(
            '("foo"."bar" = "boo"."baz") AND ("foo"."baz" = "boo"."baz")',
            $this->platform->quoteIdentifierInFragment('(foo.bar = boo.baz) AND (foo.baz = boo.baz)', array('(', ')', '=', 'and'))
        );
    }
}
