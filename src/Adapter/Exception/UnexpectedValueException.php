<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Adapter\Exception;

use Laminas\Db\Exception;

/**
 * @category   Laminas
 * @package    Laminas_Db
 * @subpackage Adapter
 */
class UnexpectedValueException extends Exception\UnexpectedValueException implements ExceptionInterface
{
}
