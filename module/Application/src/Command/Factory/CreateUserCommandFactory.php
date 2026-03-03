<?php

declare(strict_types=1);

namespace Application\Command\Factory;

use Application\Command\CreateUserCommand;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

final class CreateUserCommandFactory
{
    public function __invoke(ContainerInterface $container): CreateUserCommand
    {
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.entitymanager.orm_default');

        return new CreateUserCommand($em);
    }
}