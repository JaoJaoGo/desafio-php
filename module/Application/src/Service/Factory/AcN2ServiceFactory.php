<?php

namespace Application\Service\Factory;

use Application\Service\AcN2Service;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

final class AcN2ServiceFactory
{
    public function __invoke(ContainerInterface $container): AcN2Service
    {
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.entitymanager.orm_default');

        return new AcN2Service($em);
    }
}