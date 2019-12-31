<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Platform\IbmDb2;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Adapter\StatementContainerInterface;
use Laminas\Db\Sql\Platform\PlatformDecoratorInterface;
use Laminas\Db\Sql\Select;

class SelectDecorator extends Select implements PlatformDecoratorInterface
{
    /**
     * @var bool
     */
    protected $isSelectContainDistinct= false;

    /**
     * @var Select
     */
    protected $select = null;

    /**
     * @return bool
     */
    public function getIsSelectContainDistinct()
    {
        return $this->isSelectContainDistinct;
    }

    /**
     * @param boolean $isSelectContainDistinct
     */
    public function setIsSelectContainDistinct($isSelectContainDistinct)
    {
        $this->isSelectContainDistinct = $isSelectContainDistinct;
    }

    /**
     * @param Select $select
     */
    public function setSubject($select)
    {
        $this->select = $select;
    }

    /**
     * @see Select::renderTable
     */
    protected function renderTable($table, $alias = null)
    {
        return $table . ' ' . $alias;
    }

    /**
     * @param AdapterInterface            $adapter
     * @param StatementContainerInterface $statementContainer
     */
    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer)
    {
        // localize variables
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }
        // set specifications
        unset($this->specifications[self::LIMIT]);
        unset($this->specifications[self::OFFSET]);

        $this->specifications['LIMITOFFSET'] = null;
        parent::prepareStatement($adapter, $statementContainer);
    }

    /**
     * @param  PlatformInterface $platform
     * @return string
     */
    public function getSqlString(PlatformInterface $platform = null)
    {
        // localize variables
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }

        unset($this->specifications[self::LIMIT]);
        unset($this->specifications[self::OFFSET]);
        $this->specifications['LIMITOFFSET'] = null;

        return parent::getSqlString($platform);
    }

    /**
     * @param  PlatformInterface  $platform
     * @param  DriverInterface    $driver
     * @param  ParameterContainer $parameterContainer
     * @param  array              $sqls
     * @param  array              $parameters
     */
    protected function processLimitOffset(PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null, &$sqls, &$parameters)
    {
        if ($this->limit === null && $this->offset === null) {
            return;
        }

        $selectParameters = $parameters[self::SELECT];

        $starSuffix = $platform->getIdentifierSeparator() . self::SQL_STAR;
        foreach ($selectParameters[0] as $i => $columnParameters) {
            if ($columnParameters[0] == self::SQL_STAR
                || (isset($columnParameters[1]) && $columnParameters[1] == self::SQL_STAR)
                || strpos($columnParameters[0], $starSuffix)
            ) {
                $selectParameters[0] = array(array(self::SQL_STAR));
                break;
            }

            if (isset($columnParameters[1])) {
                array_shift($columnParameters);
                $selectParameters[0][$i] = $columnParameters;
            }
        }

        // first, produce column list without compound names (using the AS portion only)
        array_unshift($sqls, $this->createSqlFromSpecificationAndParameters(
            array('SELECT %1$s FROM (' => current($this->specifications[self::SELECT])),
            $selectParameters
        ));

        if (preg_match('/DISTINCT/i', $sqls[0])) {
            $this->setIsSelectContainDistinct(true);
        }

        if ($parameterContainer) {
            // create bottom part of query, with offset and limit using row_number
            $limitParamName        = $driver->formatParameterName('limit');
            $offsetParamName       = $driver->formatParameterName('offset');
            $offsetForSumParamName = $driver->formatParameterName('offsetForSum');

            array_push($sqls, sprintf(
                ") AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN %s AND %s",
                $offsetParamName,
                $limitParamName
            ));

            if ((int) $this->offset > 0) {
                $parameterContainer->offsetSet('offset', (int) $this->offset + 1);
            } else {
                $parameterContainer->offsetSet('offset', (int) $this->offset);
            }

            $parameterContainer->offsetSet('limit', (int) $this->limit + (int) $this->offset);
        } else {
            if ((int) $this->offset > 0) {
                $offset = (int) $this->offset + 1;
            } else {
                $offset = (int) $this->offset;
            }

            array_push($sqls, sprintf(
                ") AS LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION WHERE LAMINAS_IBMDB2_SERVER_LIMIT_OFFSET_EMULATION.LAMINAS_DB_ROWNUM BETWEEN %d AND %d",
                $offset,
                (int) $this->limit + (int) $this->offset
            ));
        }

        if (isset($sqls[self::ORDER])) {
            $orderBy = $sqls[self::ORDER];
            unset($sqls[self::ORDER]);
        } else {
            $orderBy = '';
        }

        // add a column for row_number() using the order specification //dense_rank()
        if ($this->getIsSelectContainDistinct()) {
            $parameters[self::SELECT][0][] = array('DENSE_RANK() OVER (' . $orderBy . ')', 'LAMINAS_DB_ROWNUM');
        } else {
            $parameters[self::SELECT][0][] = array('ROW_NUMBER() OVER (' . $orderBy . ')', 'LAMINAS_DB_ROWNUM');
        }

        $sqls[self::SELECT] = $this->createSqlFromSpecificationAndParameters(
            $this->specifications[self::SELECT],
            $parameters[self::SELECT]
        );
    }
}
