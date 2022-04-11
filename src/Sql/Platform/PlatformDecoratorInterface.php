<?php

namespace Laminas\Db\Sql\Platform;

interface PlatformDecoratorInterface
{
    /**
     * @param null|object $subject
     * @return $this
     */
    public function setSubject($subject);
}
