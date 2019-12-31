<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Platform;

class SqlServer implements PlatformInterface
{

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'SQLServer';
    }

    /**
     * Get quote identifier symbol
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return array('[', ']');
    }

    /**
     * Quote identifier
     *
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        return '[' . $identifier . ']';
    }

    /**
     * Quote identifier chain
     *
     * @param string|string[] $identifierChain
     * @return string
     */
    public function quoteIdentifierChain($identifierChain)
    {
        if (is_array($identifierChain)) {
            $identifierChain = implode('].[', $identifierChain);
        }
        return '[' . $identifierChain . ']';
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
        return '\'' . str_replace('\'', '\'\'', $value) . '\'';
    }

    /**
     * Quote value list
     *
     * @param string|string[] $valueList
     * @return string
     */
    public function quoteValueList($valueList)
    {
        $valueList = str_replace('\'', '\'\'', $valueList);
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
        if ($safeWords) {
            $safeWords = array_flip($safeWords);
            $safeWords = array_change_key_case($safeWords, CASE_LOWER);
        }
        foreach ($parts as $i => $part) {
            if ($safeWords && isset($safeWords[strtolower($part)])) {
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
                    $parts[$i] = '[' . $part . ']';
            }
        }
        return implode('', $parts);
    }
}
