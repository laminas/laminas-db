<?php

namespace Laminas\Db\Sql\Platform\SqlServer\Ddl;

use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Sql\Ddl\CreateTable;
use Laminas\Db\Sql\Platform\PlatformDecoratorInterface;

use function ltrim;

class CreateTableDecorator extends CreateTable implements PlatformDecoratorInterface
{
    /** @var CreateTable */
    protected $subject;

    /**
     * @param CreateTable $subject
     * @return $this Provides a fluent interface
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return array
     */
    protected function processTable(?PlatformInterface $adapterPlatform = null)
    {
        $table = ($this->isTemporary ? '#' : '') . ltrim($this->table, '#');
        return [
            '',
            $adapterPlatform->quoteIdentifier($table),
        ];
    }
}
