<?php

namespace Laminas\Db\Adapter\Profiler;

interface ProfilerAwareInterface
{
    /**
     * @param  ProfilerInterface $profiler
     */
    public function setProfiler(ProfilerInterface $profiler);
}
