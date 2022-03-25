<?php

declare(strict_types=1);

namespace Laminas\Db\Adapter\Profiler;

interface ProfilerAwareInterface
{
    public function setProfiler(ProfilerInterface $profiler);
}
