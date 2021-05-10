<?php

namespace LaminasTest\Db\TestAsset;

use Laminas\Db\Sql;

class DeleteDecorator extends Sql\Delete implements Sql\Platform\PlatformDecoratorInterface
{
    protected $subject;

    /**
     * @param $subject
     * @return self Provides a fluent interface
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }
}
