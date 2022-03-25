<?php

declare(strict_types=1);

namespace Laminas\Db\Sql\Exception;

use Laminas\Db\Exception;

class InvalidArgumentException extends Exception\InvalidArgumentException implements ExceptionInterface
{
}
