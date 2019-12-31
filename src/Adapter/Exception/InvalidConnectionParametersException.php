<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Db\Adapter\Exception;

class InvalidConnectionParametersException extends RuntimeException implements ExceptionInterface
{
    /**
     * @var int
     */
    protected $parameters;

    public function __construct(string $message, int $parameters)
    {
        parent::__construct($message);
        $this->parameters = $parameters;
    }
}
