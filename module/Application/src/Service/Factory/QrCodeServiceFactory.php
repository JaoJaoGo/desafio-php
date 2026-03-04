<?php

declare(strict_types=1);

namespace Application\Service\Factory;

use Application\Service\QrCodeService;
use Psr\Container\ContainerInterface;

final class QrCodeServiceFactory
{
    public function __invoke(ContainerInterface $container): QrCodeService
    {
        return new QrCodeService();
    }
}