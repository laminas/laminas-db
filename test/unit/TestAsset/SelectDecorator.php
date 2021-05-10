<?php

namespace LaminasTest\Db\TestAsset;

use Laminas\Db\Sql;

class SelectDecorator extends Sql\Select implements Sql\Platform\PlatformDecoratorInterface
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
