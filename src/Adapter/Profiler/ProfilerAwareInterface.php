<?php

namespace Laminas\Db\Adapter\Profiler;

interface ProfilerAwareInterface
{
    public function setProfiler(ProfilerInterface $profiler);
}
