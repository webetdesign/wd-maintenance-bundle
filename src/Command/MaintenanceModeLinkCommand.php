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
    name: 'app:maintenance-mode-link',
    description: 'Generate the link to bypass maintenance mode',
)]
class MaintenanceModeLinkCommand extends Command
{

    public function __construct(private MaintenanceService $maintenanceService)
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Link : ' . $this->maintenanceService->generateWhiteLink());

        return Command::SUCCESS;
    }


}
