<?php

namespace WebEtDesign\MaintenanceBundle\EventSubscriber;

use WebEtDesign\MaintenanceBundle\Services\MaintenanceService;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    public function __construct(private string $template, private MaintenanceService $maintenanceService, private Environment $twig){}

    public function onKernelRequest(RequestEvent $event){

        $this->maintenanceService->setHost($event->getRequest()->headers->get('host'));
        
        if (!$this->maintenanceService->maintenanceIsEnable()) return;

        if (in_array($event->getRequest()->getClientIp(), $this->maintenanceService->getIps())) return;

        try {
            $event->setResponse(
                new Response(
                    $this->twig->render($this->template),
                    Response::HTTP_TEMPORARY_REDIRECT
                )
            );
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
        }

        $event->stopPropagation();
    }

    #[ArrayShape(['kernel.request' => "string"])] public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
