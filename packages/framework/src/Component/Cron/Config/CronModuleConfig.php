<?php

namespace Shopsys\FrameworkBundle\Component\Cron\Config;

use Shopsys\FrameworkBundle\Component\Cron\CronTimeInterface;

class CronModuleConfig implements CronTimeInterface
{
    /**
     * @var \Shopsys\Plugin\Cron\SimpleCronModuleInterface
     */
    protected $service;

    /**
     * @var string
     */
    protected $serviceId;

    /**
     * @var string
     */
    protected $timeMinutes;

    /**
     * @var string
     */
    protected $timeHours;

    /**
     * @var string|null
     */
    protected $instanceName;

    /**
     * @param \Shopsys\Plugin\Cron\SimpleCronModuleInterface|\Shopsys\Plugin\Cron\IteratedCronModuleInterface $service
     * @param string $serviceId
     * @param string $timeHours
     * @param string $timeMinutes
     * @param string|null $instanceName
     */
    public function __construct($service, $serviceId, $timeHours, $timeMinutes, string $instanceName = null)
    {
        $this->service = $service;
        $this->serviceId = $serviceId;
        $this->timeHours = $timeHours;
        $this->timeMinutes = $timeMinutes;
        $this->instanceName = $instanceName;
    }

    /**
     * @return \Shopsys\Plugin\Cron\SimpleCronModuleInterface|\Shopsys\Plugin\Cron\IteratedCronModuleInterface
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return string
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @return string
     */
    public function getTimeMinutes()
    {
        return $this->timeMinutes;
    }

    /**
     * @return string
     */
    public function getTimeHours()
    {
        return $this->timeHours;
    }

    /**
     * @return string|null
     */
    public function getInstanceName(): ?string
    {
        return $this->instanceName;
    }
}
