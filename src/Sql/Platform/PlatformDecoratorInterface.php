<?php

namespace Laminas\Db\Sql\Platform;

interface PlatformDecoratorInterface
{
    /**
     * @param $subject
     *
     * @return self
     */
    public function setSubject($subject);
}
