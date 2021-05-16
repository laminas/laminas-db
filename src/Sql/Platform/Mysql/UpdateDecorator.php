<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Sql\Platform\Mysql;

use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Sql\Ddl\Column\JsonRemove;
use Laminas\Db\Sql\Platform\PlatformDecoratorInterface;
use Laminas\Db\Sql\Update;

class UpdateDecorator extends Update implements PlatformDecoratorInterface
{
    /**
     * @var Update
     */
    protected $subject = null;

    /**
     * @param Update $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    protected function processSet(
        PlatformInterface $platform,
        DriverInterface $driver = null,
        ParameterContainer $parameterContainer = null
    ): string {
        $setSql = [];
        $i = 0;
        foreach ($this->set as $column => $value) {
            if (strpos($column, '->')) {
                $setSql[] = $this->processJsonColumnValue($column, $value, $platform, $driver, $parameterContainer);

                continue;
            }
            $setSql[] = $this->processColumnValue($column, $value, $i, $platform, $driver, $parameterContainer);
            $i++;
        }

        return sprintf(
            $this->specifications[static::SPECIFICATION_SET],
            implode(', ', $setSql)
        );
    }

    /**
     * @param $column
     * @param $value
     * @param PlatformInterface $platform
     * @param DriverInterface|null $driver
     * @param ParameterContainer|null $parameterContainer
     * @return string
     */
    protected function processJsonColumnValue(
        $column,
        $value,
        PlatformInterface $platform,
        DriverInterface $driver = null,
        ParameterContainer $parameterContainer = null
    ): string {
        [$json_column, $json_path] = explode('->', $column);
        $json_column = $this
            ->resolveColumnValue(
                [
                    'column'       => $json_column,
                    'fromTable'    => '',
                    'isIdentifier' => true,
                ],
                $platform,
                $driver,
                $parameterContainer,
                'column'
            );

        if ($value instanceof JsonRemove) {
            return "{$json_column} = JSON_REMOVE({$json_column}, '$.{$json_path}')";
        } else {
            $value = $this->resolveColumnValue(
                $value,
                $platform,
                $driver,
                $parameterContainer
            );

            return "{$json_column} = JSON_SET({$json_column}, '$.{$json_path}', {$value})";
        }
    }
}
