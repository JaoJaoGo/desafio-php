<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Session\Container as SessionContainer;

final class AuthService
{
    private SessionContainer $session;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        $this->session = new SessionContainer('auth');
    }

    public function attempt(string $email, string $plainPassword): bool
    {
        $email = mb_strtolower(trim($email));

        /** @var User|null $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user === null) {
            return false;
        }

        if (!$user->verifyPassword($plainPassword)) {
            return false;
        }

        $this->session->offsetSet('user_id', $user->getId());
        $this->session->getManager()->regenerateId(true);

        return true;
    }

    public function logout(): void
    {
        $this->session->getManager()->getStorage()->clear('auth');
    }

    public function check(): bool
    {
        return $this->session->offsetExists('user_id') && is_int($this->session->offsetGet('user_id'));
    }

    public function userId(): ?int
    {
        $id = $this->session->offsetGet('user_id');

        return is_int($id) ? $id : null;
    }
}