<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

use NinjaMutex\Lock\LockInterface;
use NinjaMutex\Mutex;

class MutexFactory
{
    const MUTEX_CRON_NAME = 'cron';

    /**
     * @var \NinjaMutex\Lock\LockInterface
     */
    protected $lock;

    /**
     * @var \NinjaMutex\Mutex[]
     */
    protected $mutexesByName;

    /**
     * @param \NinjaMutex\Lock\LockInterface $lock
     */
    public function __construct(LockInterface $lock)
    {
        $this->lock = $lock;
        $this->mutexesByName = [];
    }

    /**
     * @param string|null $prefix
     * @return \NinjaMutex\Mutex
     */
    public function getCronMutex(string $prefix = null)
    {
        $lockName = self::MUTEX_CRON_NAME;

        if ($prefix !== null) {
            $lockName = $prefix . '-' . $lockName;
        }

        if (!array_key_exists($lockName, $this->mutexesByName)) {
            $this->mutexesByName[$lockName] = new Mutex($lockName, $this->lock);
        }

        return $this->mutexesByName[$lockName];
    }
}
