<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Command;

use DateTime;
use DateTimeImmutable;
use Shopsys\FrameworkBundle\Command\Exception\CronCommandException;
use Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig;
use Shopsys\FrameworkBundle\Component\Cron\CronFacade;
use Shopsys\FrameworkBundle\Component\Cron\MutexFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CronInstanceCommand extends Command
{
    protected const OPTION_MODULE = 'module';
    protected const OPTION_LIST = 'list';
    protected const OPTION_LIST_INSTANCES = 'list-instances';
    protected const ARGUMENT_INSTANCE_NAME = 'instance-name';

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:cron-instance';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Cron\CronFacade
     */
    private $cronFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Cron\MutexFactory
     */
    private $mutexFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\CronFacade $cronFacade
     * @param \Shopsys\FrameworkBundle\Component\Cron\MutexFactory $mutexFactory
     */
    public function __construct(
        CronFacade $cronFacade,
        MutexFactory $mutexFactory
    ) {
        $this->cronFacade = $cronFacade;
        $this->mutexFactory = $mutexFactory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Runs background jobs for specific instance only. Should be executed periodically by system CRON every 5 minutes.')
            ->addOption(self::OPTION_LIST, null, InputOption::VALUE_NONE, 'List all Service commands')
            ->addOption(self::OPTION_LIST_INSTANCES, null, InputOption::VALUE_NONE, 'List all registered cron instances')
            ->addOption(self::OPTION_MODULE, null, InputOption::VALUE_OPTIONAL, 'Service ID')
            ->addArgument(self::ARGUMENT_INSTANCE_NAME, null, 'Instance to be run');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $optionListInstances = $input->getOption(self::OPTION_LIST_INSTANCES);
        $optionList = $input->getOption(self::OPTION_LIST);
        $instanceName = $input->getArgument(self::ARGUMENT_INSTANCE_NAME);

        if ($optionListInstances) {
            $this->listAllInstances($output);

            return;
        }

        if ($instanceName === null) {
            throw new RuntimeException(sprintf('Not enough arguments (missing: "%s").', self::ARGUMENT_INSTANCE_NAME));
        }

        if ($optionList === true) {
            $this->listAllCronModulesSortedByServiceId($instanceName, $output);

            return;
        }

        $this->runCron($instanceName, $input->getOption(self::OPTION_MODULE));
    }

    /**
     * @param string $instanceName
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function listAllCronModulesSortedByServiceId(string $instanceName, OutputInterface $output): void
    {
        $cronModuleConfigs = $this->cronFacade->getAllForInstance($instanceName);

        uasort($cronModuleConfigs, function (CronModuleConfig $cronModuleConfigA, CronModuleConfig $cronModuleConfigB) {
            return $cronModuleConfigA->getServiceId() > $cronModuleConfigB->getServiceId();
        });

        foreach ($cronModuleConfigs as $cronModuleConfig) {
            $output->writeln(sprintf('php bin/console %s %s --module="%s"', $this->getName(), $instanceName, $cronModuleConfig->getServiceId()));
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function listAllInstances(OutputInterface $output): void
    {
        foreach ($this->cronFacade->getInstanceNames() as $instanceName) {
            $output->writeln($instanceName);
        }
    }

    /**
     * @param string $queueName
     * @param string|null $requestedModuleServiceId
     * @throws \Shopsys\FrameworkBundle\Command\Exception\CronCommandException
     */
    private function runCron(string $queueName, string $requestedModuleServiceId = null): void
    {
        $runAllModules = $requestedModuleServiceId === null;
        if ($runAllModules) {
            $this->cronFacade->scheduleModulesByTime($this->getCurrentRoundedTime());
        }

        $mutex = $this->mutexFactory->getCronMutex();
        if ($mutex->acquireLock(0)) {
            if ($runAllModules) {
                $this->cronFacade->runScheduledModulesForInstance($queueName);
            } else {
                $this->cronFacade->runModuleByServiceId($requestedModuleServiceId);
            }
            $mutex->releaseLock();
        } else {
            throw new CronCommandException(
                'Cron is locked. Another cron module is already running.'
            );
        }
    }

    /**
     * @return \DateTimeImmutable
     */
    private function getCurrentRoundedTime(): DateTimeImmutable
    {
        $time = new DateTime();
        $time->modify('-' . $time->format('s') . ' sec');
        $time->modify('-' . ($time->format('i') % 5) . ' min');

        return DateTimeImmutable::createFromMutable($time);
    }
}
