<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Platform;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Adapter
 */
class Sqlite implements PlatformInterface
{

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'SQLite';
    }

    /**
     * Get quote identifier symbol
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return '"';
    }

    /**
     * Quote identifier
     *
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        return '"' . str_replace('"', '\\' . '"', $identifier) . '"';
    }

    /**
     * Quote identifier chain
     *
     * @param string|string[] $identifierChain
     * @return string
     */
    public function quoteIdentifierChain($identifierChain)
    {
        $identifierChain = str_replace('"', '\\"', $identifierChain);
        if (is_array($identifierChain)) {
            $identifierChain = implode('"."', $identifierChain);
        }
        return '"' . $identifierChain . '"';
    }

    /**
     * Get quote value symbol
     *
     * @return string
     */
    public function getQuoteValueSymbol()
    {
        return '\'';
    }

    /**
     * Quote value
     *
     * @param  string $value
     * @return string
     */
    public function quoteValue($value)
    {
        return '\'' . str_replace('\'', '\\' . '\'', $value) . '\'';
    }

    /**
     * Quote value list
     *
     * @param string|string[] $valueList
     * @return string
     */
    public function quoteValueList($valueList)
    {
        $valueList = str_replace('\'', '\\' . '\'', $valueList);
        if (is_array($valueList)) {
            $valueList = implode('\', \'', $valueList);
        }
        return '\'' . $valueList . '\'';
    }

    /**
     * Get identifier separator
     *
     * @return string
     */
    public function getIdentifierSeparator()
    {
        return '.';
    }

    /**
     * Quote identifier in fragment
     *
     * @param  string $identifier
     * @param  array $safeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = array())
    {
        $parts = preg_split('#([\.\s\W])#', $identifier, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ($parts as $i => $part) {
            if ($safeWords && in_array($part, $safeWords)) {
                continue;
            }
            switch ($part) {
                case ' ':
                case '.':
                case '*':
                case 'AS':
                case 'As':
                case 'aS':
                case 'as':
                    break;
                default:
                    $parts[$i] = '"' . str_replace('"', '\\' . '"', $part) . '"';
            }
        }
        return implode('', $parts);
    }

}
