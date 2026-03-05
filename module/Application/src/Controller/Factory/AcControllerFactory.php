<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\AcController;
use Application\Service\AcService;
use Application\Service\QrCodeService;
use Psr\Container\ContainerInterface;

final class AcControllerFactory
{
    public function __invoke(ContainerInterface $container): AcController
    {
        $config = $container->get('config');

        $baseUrl = (string)($config['app']['base_url'] ?? 'http://localhost:8080');
        $baseUrl = rtrim($baseUrl, '/');

        return new AcController(
            $container->get(AcService::class),
            $container->get(QrCodeService::class),
            $baseUrl,
        );
    }
}