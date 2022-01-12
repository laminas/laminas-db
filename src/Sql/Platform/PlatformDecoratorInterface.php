<?php

namespace Laminas\Db\Sql\Platform;

interface PlatformDecoratorInterface
{
    /**
     * @param null|object $subject
     * @return self
     */
    public function setSubject($subject);
}
