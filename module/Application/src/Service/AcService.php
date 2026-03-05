<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Entity\Ac;
use Application\Entity\AcN2;
use Doctrine\ORM\EntityManagerInterface;

final class AcService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /** @return list<Ac> */
    public function list(): array
    {
        return $this->em->getRepository(Ac::class)->findBy([], ['id' => 'DESC']);
    }

    public function find(int $id): ?Ac
    {
        /** @var Ac|null $ac */
        $ac = $this->em->find(Ac::class, $id);
        return $ac;
    }

    /** @return list<AcN2> */
    public function listChildren(Ac $ac): array
    {
        return $this->em->getRepository(AcN2::class)->findBy(
            ['ac' => $ac],
            ['id' => 'DESC']
        );
    }

    public function create(string $name): Ac
    {
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('O nome é obrigatório.');
        }

        $ac = new Ac($name);
        $this->em->persist($ac);
        $this->em->flush();

        return $ac;
    }

    public function update(Ac $ac, string $name): Ac
    {
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('O nome é obrigatório.');
        }

        $ac->setName($name);
        $this->em->flush();

        return $ac;
    }

    public function delete(Ac $ac): void
    {
        $this->em->remove($ac);
        $this->em->flush();
    }
}