<?php

namespace Laminas\Db\Adapter\Profiler;

interface ProfilerInterface
{
    /**
     * @param string|\Laminas\Db\Adapter\StatementContainerInterface $target
     * @return mixed
     */
    public function profilerStart($target);
    public function profilerFinish();
}
