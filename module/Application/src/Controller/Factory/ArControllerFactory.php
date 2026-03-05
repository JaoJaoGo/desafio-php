<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\ArController;
use Application\Service\ArService;
use Application\Service\QrCodeService;
use Psr\Container\ContainerInterface;

final class ArControllerFactory
{
    public function __invoke(ContainerInterface $container): ArController
    {
        return new ArController(
            $container->get(ArService::class),
            $container->get(QrCodeService::class),
            $container->get('config')['app']['base_url'] ?? 'http://localhost:8080',
        );
    }
}