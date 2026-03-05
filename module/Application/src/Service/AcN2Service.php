<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Entity\Ac;
use Application\Entity\AcN2;
use Application\Entity\Ar;
use Doctrine\ORM\EntityManagerInterface;

final class AcN2Service
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /** @return list<AcN2> */
    public function list(): array
    {
        return $this->em->getRepository(AcN2::class)->findBy([], ['id' => 'DESC']);
    }

    public function find(int $id): ?AcN2
    {
        /** @var AcN2|null $acn2 */
        $acn2 = $this->em->find(AcN2::class, $id);

        return $acn2;
    }

    /** @return list<Ac> */
    public function listAcs(): array
    {
        return $this->em->getRepository(Ac::class)->findBy([], ['id' => 'DESC']);
    }

    public function findAc(int $id): ?Ac
    {
        /** @var Ac|null $ac */
        $ac = $this->em->find(Ac::class, $id);

        return $ac;
    }

    /** @return list<Ar> */
    public function listChildren(AcN2 $acn2): array
    {
        return $this->em->getRepository(Ar::class)->findBy(
            ['acN2' => $acn2],
            ['id' => 'DESC']
        );
    }

    public function create(Ac $ac, string $name): AcN2
    {
        $name = trim($name);

        if ($name === '') {
            throw new \InvalidArgumentException('O nome é obrigatório.');
        }

        $acn2 = new AcN2($ac, $name);

        $this->em->persist($acn2);
        $this->em->flush();

        return $acn2;
    }

    public function update(AcN2 $acn2, Ac $ac, string $name): AcN2
    {
        $name = trim($name);

        if ($name === '') {
            throw new \InvalidArgumentException('O nome é obrigatório.');
        }

        $acn2->setName($name);
        $acn2->setAc($ac);

        $this->em->flush();

        return $acn2;
    }

    public function delete(AcN2 $acn2): void
    {
        $this->em->remove($acn2);
        $this->em->flush();
    }
}