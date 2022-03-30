<?php

namespace Laminas\Db\Sql\Platform\SqlServer;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Sql\Platform\PlatformDecoratorInterface;
use Laminas\Db\Sql\Select;

use function array_push;
use function array_shift;
use function array_unshift;
use function array_values;
use function current;
use function strpos;

class SelectDecorator extends Select implements PlatformDecoratorInterface
{
    /** @var Select */
    protected $subject;

    /**
     * @param Select $select
     */
    public function setSubject($select)
    {
        $this->subject = $select;
    }

    protected function localizeVariables()
    {
        parent::localizeVariables();
        // set specifications
        unset($this->specifications[self::LIMIT]);
        unset($this->specifications[self::OFFSET]);

        $this->specifications['LIMITOFFSET'] = null;
    }

    /**
     * @param string[] $sqls
     * @param array<string, array> $parameters
     * @return void
     */
    protected function processLimitOffset(
        PlatformInterface $platform,
        ?DriverInterface $driver,
        ?ParameterContainer $parameterContainer,
        &$sqls,
        &$parameters
    ) {
        if ($this->limit === null && $this->offset === null) {
            return;
        }

        $selectParameters = $parameters[self::SELECT];

        /** if this is a DISTINCT query then real SELECT part goes to second element in array **/
        $parameterIndex = 0;
        if ($selectParameters[0] === 'DISTINCT') {
            unset($selectParameters[0]);
            $selectParameters = array_values($selectParameters);
            $parameterIndex   = 1;
        }

        $starSuffix = $platform->getIdentifierSeparator() . self::SQL_STAR;
        foreach ($selectParameters[0] as $i => $columnParameters) {
            if (
                $columnParameters[0] === self::SQL_STAR
                || (isset($columnParameters[1]) && $columnParameters[1] === self::SQL_STAR)
                || strpos($columnParameters[0], $starSuffix)
            ) {
                $selectParameters[0] = [[self::SQL_STAR]];
                break;
            }
            if (isset($columnParameters[1])) {
                array_shift($columnParameters);
                $selectParameters[0][$i] = $columnParameters;
            }
        }

        // first, produce column list without compound names (using the AS portion only)
        array_unshift($sqls, $this->createSqlFromSpecificationAndParameters(
            ['SELECT %1$s FROM (' => current($this->specifications[self::SELECT])],
            $selectParameters
        ));

        // phpcs:disable Generic.Files.LineLength.TooLong

        if ($parameterContainer) {
            // create bottom part of query, with offset and limit using row_number
            $limitParamName        = $driver->formatParameterName('limit');
            $offsetParamName       = $driver->formatParameterName('offset');
            $offsetForSumParamName = $driver->formatParameterName('offsetForSum');
            array_push(
                $sqls,
                ') AS [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__LAMINAS_ROW_NUMBER] BETWEEN '
                . $offsetParamName
                . '+1 AND '
                . $limitParamName
                . '+'
                . $offsetForSumParamName
            );
            $parameterContainer->offsetSet('offset', $this->offset);
            $parameterContainer->offsetSet('limit', $this->limit);
            $parameterContainer->offsetSetReference('offsetForSum', 'offset');
        } else {
            array_push(
                $sqls,
                ') AS [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__LAMINAS_ROW_NUMBER] BETWEEN '
                . (int) $this->offset
                . '+1 AND '
                . (int) $this->limit
                . '+'
                . (int) $this->offset
            );
        }

        // phpcs:enable Generic.Files.LineLength.TooLong

        if (isset($sqls[self::ORDER])) {
            $orderBy = $sqls[self::ORDER];
            unset($sqls[self::ORDER]);
        } else {
            $orderBy = 'ORDER BY (SELECT 1)';
        }

        // add a column for row_number() using the order specification
        $parameters[self::SELECT][$parameterIndex][] = [
            'ROW_NUMBER() OVER (' . $orderBy . ')',
            '[__LAMINAS_ROW_NUMBER]',
        ];

        $sqls[self::SELECT] = $this->createSqlFromSpecificationAndParameters(
            $this->specifications[self::SELECT],
            $parameters[self::SELECT]
        );
    }
}
