<?php

namespace Laminas\Db\Adapter\Profiler;

use Laminas\Db\Adapter\Exception;
use Laminas\Db\Adapter\Exception\InvalidArgumentException;
use Laminas\Db\Adapter\StatementContainerInterface;

use function end;
use function is_string;
use function microtime;

class Profiler implements ProfilerInterface
{
    /** @var array */
    protected $profiles = [];

    /** @var null */
    protected $currentIndex = 0;

    /**
     * @param string|StatementContainerInterface $target
     * @return self Provides a fluent interface
     * @throws InvalidArgumentException
     */
    public function profilerStart($target)
    {
        $profileInformation = [
            'sql'        => '',
            'parameters' => null,
            'start'      => microtime(true),
            'end'        => null,
            'elapse'     => null,
        ];
        if ($target instanceof StatementContainerInterface) {
            $profileInformation['sql']        = $target->getSql();
            $profileInformation['parameters'] = clone $target->getParameterContainer();
        } elseif (is_string($target)) {
            $profileInformation['sql'] = $target;
        } else {
            throw new Exception\InvalidArgumentException(
                __FUNCTION__ . ' takes either a StatementContainer or a string'
            );
        }

        $this->profiles[$this->currentIndex] = $profileInformation;

        return $this;
    }

    /**
     * @return self Provides a fluent interface
     */
    public function profilerFinish()
    {
        if (! isset($this->profiles[$this->currentIndex])) {
            throw new Exception\RuntimeException(
                'A profile must be started before ' . __FUNCTION__ . ' can be called.'
            );
        }
        $current           = &$this->profiles[$this->currentIndex];
        $current['end']    = microtime(true);
        $current['elapse'] = $current['end'] - $current['start'];
        $this->currentIndex++;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getLastProfile()
    {
        return end($this->profiles);
    }

    /**
     * @return array
     */
    public function getProfiles()
    {
        return $this->profiles;
    }
}
