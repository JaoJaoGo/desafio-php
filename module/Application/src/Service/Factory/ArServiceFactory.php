<?php

declare(strict_types=1);

namespace Application\Service\Factory;

use Application\Service\ArService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

final class ArServiceFactory
{
    public function __invoke(ContainerInterface $container): ArService
    {
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.entitymanager.orm_default');

        return new ArService($em);
    }
}