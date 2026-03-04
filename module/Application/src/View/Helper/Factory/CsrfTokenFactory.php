<?php

declare(strict_types=1);

namespace Application\View\Helper\Factory;

use Application\Service\CsrfService;
use Application\View\Helper\CsrfToken;
use Psr\Container\ContainerInterface;

final class CsrfTokenFactory
{
    public function __invoke(ContainerInterface $container): CsrfToken
    {
        return new CsrfToken($container->get(CsrfService::class));
    }
}