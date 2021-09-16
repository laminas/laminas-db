<?php

namespace Laminas\Db\Adapter\Driver\Sqlsrv\Exception;

use Laminas\Db\Adapter\Exception;

use function sqlsrv_errors;

class ErrorException extends Exception\ErrorException implements ExceptionInterface
{
    /**
     * Errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Construct
     *
     * @param  bool $errors
     */
    public function __construct($errors = false)
    {
        $this->errors = $errors === false ? sqlsrv_errors() : $errors;
    }
}
