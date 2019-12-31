<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Driver\Sqlsrv\Exception;

use Laminas\Db\Adapter\Exception;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Adapter
 */
class ErrorException extends Exception\ErrorException implements ExceptionInterface
{

    /**
     * Errors
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Construct
     *
     * @param  bool $errors
     */
    public function __construct($errors = false)
    {
        $this->errors = ($errors === false) ? sqlsrv_errors() : $errors;
    }
}
