<?php

namespace LaminasTest\Db\TestAsset;

use Laminas\Db\Sql;

class UpdateDecorator extends Sql\Update implements Sql\Platform\PlatformDecoratorInterface
{
    /** @var null|object $subject */
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
