<?php

declare(strict_types=1);

namespace Application\Service\Factory;

use Application\Service\CsrfService;
use Psr\Container\ContainerInterface;

final class CsrfServiceFactory
{
    public function __invoke(ContainerInterface $container): CsrfService
    {
        return new CsrfService();
    }
}