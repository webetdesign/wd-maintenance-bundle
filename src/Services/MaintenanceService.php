<?php

namespace WebEtDesign\MaintenanceBundle\Services;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WebEtDesign\CmsBundle\Entity\CmsSite;

class MaintenanceService
{
    private Filesystem $filesystem;

    public const MAINTENANCE_FILE = __DIR__ . '/../../../../../var';

    #[Pure] public function __construct(private UrlGeneratorInterface $urlGenerator, private ?string $host = null)
    {
        $this->filesystem = new Filesystem();
    }

    public function enableMaintenance(?string $ipsArgs = '', SymfonyStyle $io = null, array $sites = [])
    {
        $this->disableMaintenance(true, $io, $sites);

        /** @var CmsSite $site */
        foreach ($sites as $site) {
            $this->setHost($site->getHost());
            $this->filesystem->touch($this->getMaintenancePath(false));

            $this->filesystem->appendToFile($this->getMaintenancePath(), str_replace(' ', '', $ipsArgs));
        }

        $io?->success('Maintenance mode enabled');
    }

    public function disableMaintenance(bool $check = false, SymfonyStyle $io = null, array $sites = [])
    {
        $this->filesystem = new Filesystem();

        /** @var CmsSite $site */
        foreach ($sites as $site) {
            $this->setHost($site->getHost());
            if ($this->filesystem->exists($this->getMaintenancePath())){
                $this->filesystem->remove($this->getMaintenancePath());
                if (!$check) $io?->success('Maintenance mode disabled');
            }else{
                if (!$check) $io?->error('Maintenance mode is not enabled');
            }
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
    
    private function getMaintenancePath (bool $test = true): bool|string
    {
        if ($test){
            return realpath(self::MAINTENANCE_FILE . "/.maintenance-$this->host") ?: realpath(self::MAINTENANCE_FILE . '/.maintenance') ;
        }else{
            return realpath(self::MAINTENANCE_FILE) . "/.maintenance" . ($this->host ? "-$this->host" : '');
        }
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string|null $host
     * @return MaintenanceService
     */
    public function setHost(?string $host): MaintenanceService
    {
        $this->host = $host;
        return $this;
    }

    public function isAuthorized(Request $request): bool
    {
        if (in_array($request->getClientIp(), $this->getIps())) return true;

        if ($this->validWhiteLink($request->cookies->get('MAINTENANCE_WHITE_LINK', -1))) return true;

        return false;
    }

    public function validWhiteLink(string $hash): bool
    {
        return $hash === $_ENV['MAINTENANCE_BUNDLE_HASH'];
    }

    public function generateWhiteLink(): string
    {
        return $this->urlGenerator->generate('maintenance_authorize', ['hash' => $_ENV['MAINTENANCE_BUNDLE_HASH']], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}