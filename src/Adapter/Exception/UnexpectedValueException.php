<?php

declare(strict_types=1);

namespace Laminas\Db\Adapter\Exception;

use Laminas\Db\Exception;

class UnexpectedValueException extends Exception\UnexpectedValueException implements ExceptionInterface
{
}
