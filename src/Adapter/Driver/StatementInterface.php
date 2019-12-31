<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Driver;

use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\StatementContainerInterface;

interface StatementInterface extends StatementContainerInterface
{
    /**
     * @return mixed
     */
    public function getResource();

    public function prepare(string $sql = null): StatementInterface;

    public function isPrepared(): bool;

    public function execute(array $parameters = null): ResultInterface;
}
