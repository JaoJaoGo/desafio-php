<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Entity\AcN2;
use Application\Entity\Ar;
use Doctrine\ORM\EntityManagerInterface;

final class ArService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /** @return list<Ar> */
    public function list(): array
    {
        return $this->em->getRepository(Ar::class)->findBy([], ['id' => 'DESC']);
    }

    public function find(int $id): ?Ar
    {
        /** @var Ar|null $ar */
        $ar = $this->em->find(Ar::class, $id);
        return $ar;
    }

    /** @return list<AcN2> */
    public function listAcN2s(): array
    {
        return $this->em->getRepository(AcN2::class)->findBy([], ['id' => 'DESC']);
    }

    public function findAcN2(int $id): ?AcN2
    {
        /** @var AcN2|null $acn2 */
        $acn2 = $this->em->find(AcN2::class, $id);
        return $acn2;
    }

    public function create(AcN2 $acN2, string $name): Ar
    {
        $name = trim($name);

        if ($name === '') {
            throw new \InvalidArgumentException('O nome é obrigatório.');
        }

        $ar = new Ar($acN2, $name);

        $this->em->persist($ar);
        $this->em->flush();

        return $ar;
    }

    public function update(Ar $ar, AcN2 $acN2, string $name): Ar
    {
        $name = trim($name);

        if ($name === '') {
            throw new \InvalidArgumentException('O nome é obrigatório.');
        }

        $ar->setName($name);
        $ar->setAcN2($acN2);

        $this->em->flush();

        return $ar;
    }

    public function delete(Ar $ar): void
    {
        $this->em->remove($ar);
        $this->em->flush();
    }
}