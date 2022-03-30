<?php

namespace Laminas\Db\Adapter\Profiler;

use Laminas\Db\Adapter\StatementContainerInterface;

interface ProfilerInterface
{
    /**
     * @param string|StatementContainerInterface $target
     * @return mixed
     */
    public function profilerStart($target);

    public function profilerFinish();
}
