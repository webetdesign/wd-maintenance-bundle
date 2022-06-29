<?php

namespace WebEtDesign\MaintenanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use WebEtDesign\MaintenanceBundle\Services\MaintenanceService;

class WhiteLinkController extends AbstractController
{
    public function __construct(private MaintenanceService $maintenanceService){}

    #[Route(path: "/maintenance/authorize/{hash}", name: "maintenance_authorize")]
    public function __invoke(Request $request, string $hash): Response
    {
        if (!$this->maintenanceService->validWhiteLink($hash)) throw new UnauthorizedHttpException('');

        $response = new RedirectResponse('/');
        $response->headers->setCookie(Cookie::create('MAINTENANCE_WHITE_LINK', $hash));

        return $response;
    }
}