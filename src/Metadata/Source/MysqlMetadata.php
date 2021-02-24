<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Metadata\Source;

/**
 * Metadata source for MySQL
 */
class MysqlMetadata extends AnsiMetadata
{
    protected function prepareLoadColumnDataColumns(array &$columns)
    {
        $columns[] = ['C', 'COLUMN_TYPE'];
    }

    protected function postLoadColumnDataResult(&$row)
    {
        $erratas = [];
        $matches = [];
        if (preg_match('/^(?:enum|set)\((.+)\)$/i', $row['COLUMN_TYPE'], $matches)) {
            $permittedValues = $matches[1];
            if (preg_match_all(
                "/\\s*'((?:[^']++|'')*+)'\\s*(?:,|\$)/",
                $permittedValues,
                $matches,
                PREG_PATTERN_ORDER
            )
            ) {
                $permittedValues = str_replace("''", "'", $matches[1]);
            } else {
                $permittedValues = [$permittedValues];
            }
            $erratas['permitted_values'] = $permittedValues;
        }
        $row[AnsiMetadata::ERRATAS] = $erratas;
        $row[AnsiMetadata::NUMERIC_UNSIGNED] = (false !== strpos($row['COLUMN_TYPE'], 'unsigned'));
    }
}
