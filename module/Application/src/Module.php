<?php

declare(strict_types=1);

namespace Application;

use Application\Command\CreateUserCommand;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\InitProviderInterface;
use Laminas\ModuleManager\ModuleManagerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;

final class Module implements ConfigProviderInterface, InitProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * DoctrineModule CLI integration point.
     * Adds custom Symfony Console commands to doctrine-module CLI.
     */
    public function init(ModuleManagerInterface $manager): void
    {
        $sharedEvents = $manager->getEventManager()->getSharedManager();

        $sharedEvents->attach('doctrine', 'loadCli.post', function (EventInterface $e): void {
            $cli = $e->getTarget();

            if (!$cli instanceof ConsoleApplication) {
                return;
            }

            $container = $e->getParam('ServiceManager');
            $cli->add($container->get(CreateUserCommand::class));
        });
    }
}