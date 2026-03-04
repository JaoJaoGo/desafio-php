<?php

declare(strict_types=1);

namespace Application\Service\Factory;

use Application\Service\AuthService;
use Psr\Container\ContainerInterface;

final class AuthServiceFactory
{
    public function __invoke(ContainerInterface $container): AuthService
    {
        $em = $container->get('doctrine.entitymanager.orm_default');

        return new AuthService($em);
    }
}