<?php

declare(strict_types=1);

namespace Laminas\Db\RowGateway\Exception;

use Laminas\Db\Exception;

class RuntimeException extends Exception\RuntimeException implements ExceptionInterface
{
}
