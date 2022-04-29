<?php

namespace WebEtDesign\MaintenanceBundle\Services;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class MaintenanceService
{
    private Filesystem $filesystem;

    public const MAINTENANCE_FILE = __DIR__ . '/../../../../../public';

    #[Pure] public function __construct()
    {
        $this->filesystem = new Filesystem();
    }


    public function enableMaintenance(?string $ipsArgs = '', SymfonyStyle $io = null)
    {
        $this->disableMaintenance(true, $io);

        $this->filesystem->touch($this->getMaintenancePath());

        $this->filesystem->appendToFile($this->getMaintenancePath(), $ipsArgs);

        $io?->success('Maintenance mode enabled');
    }

    public function disableMaintenance(bool $check = false, SymfonyStyle $io = null)
    {
        $this->filesystem = new Filesystem();
        
        if ($this->filesystem->exists($this->getMaintenancePath())){
            $this->filesystem->remove($this->getMaintenancePath());
            if (!$check) $io?->success('Maintenance mode disabled');
        }else{
            if (!$check) $io?->error('Maintenance mode is not enabled');
        }
    }

    public function maintenanceIsEnable (): bool
    {
        return $this->filesystem->exists($this->getMaintenancePath());
    }

    public function getIps (): array
    {
        return $this->maintenanceIsEnable() ? explode(',', file_get_contents($this->getMaintenancePath())) : [];
    }
    
    private function getMaintenancePath (){
        return realpath(self::MAINTENANCE_FILE) . '/.maintenance';
    }
}