<?php

namespace App\EventSubscriber;

use App\Repository\ConfiguracionRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class ConfiguracionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ConfiguracionRepository $configuracionRepository,
        private Environment $twig
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        $config = $this->configuracionRepository->getAjustes();
        $this->twig->addGlobal('config', $config);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}