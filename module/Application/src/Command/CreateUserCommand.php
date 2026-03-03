<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        // Explicit command name to avoid "empty name" issues across Symfony Console versions.
        parent::__construct('app:user:create');
    }

    protected function configure(): void
    {
        $this->setDescription('Create an initial user (idempotent).');

        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'User email', 'admin@admin.com')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'User password', 'admin123')
            ->addOption('force', null, InputOption::VALUE_NONE, 'If user exists, overwrite the password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = mb_strtolower(trim((string) $input->getOption('email')));
        $password = (string) $input->getOption('password');
        $force = (bool) $input->getOption('force');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $output->writeln('<error>Invalid email.</error>');
            return Command::INVALID;
        }

        if (strlen($password) < 6) {
            $output->writeln('<error>Password must be at least 6 characters.</error>');
            return Command::INVALID;
        }

        $repo = $this->em->getRepository(User::class);

        /** @var User|null $existing */
        $existing = $repo->findOneBy(['email' => $email]);

        $hash = password_hash($password, PASSWORD_DEFAULT);

        if ($existing !== null) {
            if (!$force) {
                $output->writeln(sprintf('<comment>User already exists: %s</comment>', $email));
                $output->writeln('<comment>Use --force to overwrite the password.</comment>');
                return Command::SUCCESS;
            }

            $existing->setPasswordHash($hash);
            $this->em->flush();

            $output->writeln(sprintf('<info>Password updated for: %s</info>', $email));
            return Command::SUCCESS;
        }

        $user = new User($email, $hash);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln(sprintf('<info>User created: %s</info>', $email));
        return Command::SUCCESS;
    }
}