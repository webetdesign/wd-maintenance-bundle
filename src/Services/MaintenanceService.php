<?php

namespace WebEtDesign\MaintenanceBundle\Services;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class MaintenanceService
{
    private Filesystem $filesystem;

    public const MAINTENANCE_FILE = __DIR__ . '../../../../../public/.maintenance';

    #[Pure] public function __construct()
    {
        $this->filesystem = new Filesystem();
    }


    public function enableMaintenance(?string $ipsArgs = '', SymfonyStyle $io = null)
    {
        $this->disableMaintenance(true, $io);

        $this->filesystem->touch(self::MAINTENANCE_FILE);

        $this->filesystem->appendToFile(self::MAINTENANCE_FILE, $ipsArgs);

        $io?->success('Maintenance mode enabled');
    }

    public function disableMaintenance(bool $check = false, SymfonyStyle $io = null)
    {
        $this->filesystem = new Filesystem();

        if ($this->filesystem->exists(self::MAINTENANCE_FILE)){
            $this->filesystem->remove(self::MAINTENANCE_FILE);
            if (!$check) $io?->success('Maintenance mode disabled');
        }else{
            if (!$check) $io?->error('Maintenance mode is not enabled');
        }
    }

    public function maintenanceIsEnable (): bool
    {
        return $this->filesystem->exists(self::MAINTENANCE_FILE);
    }

    public function getIps (): array
    {
        return $this->maintenanceIsEnable() ? explode(',', file_get_contents(self::MAINTENANCE_FILE)) : [];
    }
}