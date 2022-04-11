<?php

namespace LaminasTest\Db\TestAsset;

use Laminas\Db\Sql;

class DeleteDecorator extends Sql\Delete implements Sql\Platform\PlatformDecoratorInterface
{
    /** @var null|object */
    protected $subject;

    /**
     * @param null|object $subject
     * @return $this Provides a fluent interface
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }
}
