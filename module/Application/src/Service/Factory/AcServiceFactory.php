<?php

declare(strict_types=1);

namespace Application\Service\Factory;

use Application\Service\AcService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

final class AcServiceFactory
{
    public function __invoke(ContainerInterface $container): AcService
    {
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.entitymanager.orm_default');

        return new AcService($em);
    }
}