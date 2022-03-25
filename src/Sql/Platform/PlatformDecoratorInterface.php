<?php

declare(strict_types=1);

namespace Laminas\Db\Sql\Platform;

interface PlatformDecoratorInterface
{
    /**
     * @param null|object $subject
     * @return self
     */
    public function setSubject($subject);
}
