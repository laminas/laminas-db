<?php
/**
 * @see       https://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Db\Adapter\Driver;

use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\StatementContainerInterface;

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
