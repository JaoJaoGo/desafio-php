<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\AcN2Controller;
use Application\Service\AcN2Service;
use Application\Service\QrCodeService;
use Psr\Container\ContainerInterface;

final class AcN2ControllerFactory
{
    public function __invoke(ContainerInterface $container): AcN2Controller
    {
        return new AcN2Controller(
            $container->get(AcN2Service::class),
            $container->get(QrCodeService::class),
            $container->get('config')['app']['base_url'] ?? 'http://localhost:8080',
        );
    }
}