<?php

declare(strict_types=1);

namespace Laminas\Db\TableGateway\Exception;

use Laminas\Db\Exception;

class RuntimeException extends Exception\InvalidArgumentException implements ExceptionInterface
{
}
