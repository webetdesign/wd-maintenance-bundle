<?php

namespace WebEtDesign\MaintenanceBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebEtDesign\MaintenanceBundle\Services\MaintenanceService;

#[AsCommand(
    name: 'app:maintenance-mode',
    description: 'Put the website in maintenance mode',
)]
class MaintenanceModeCommand extends Command
{

    public function __construct(private MaintenanceService $maintenanceService)
    {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->addOption('on', null, InputOption::VALUE_NONE, 'Make maintenance on')
            ->addOption('off', null, InputOption::VALUE_NONE, 'Make maintenance off')
            ->addArgument('ips', InputArgument::OPTIONAL, 'White list of ip address : "XX.XX.XX.XX, XX.XX.XX.XX"')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $ipsArgs = $input->getArgument('ips');

        if ($ipsArgs) {
            $io->note(sprintf('The following ips will be added to the white list : %s', $ipsArgs));
        }

        if ($input->getOption('on')){
            $this->maintenanceService->enableMaintenance($ipsArgs, $io);
        }else if ($input->getOption('off')){
            $this->maintenanceService->disableMaintenance(false, $io);
        }else{
            $io->error('You need to pass on or off option');
            return Command::INVALID;
        }

        return Command::SUCCESS;
    }


}
