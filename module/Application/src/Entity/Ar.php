<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'ars')]
#[ORM\Index(name: 'idx_ar_ac_n2_id', columns: ['ac_n2_id'])]
#[ORM\HasLifecycleCallbacks]
class Ar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 150)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: AcN2::class, inversedBy: 'ars')]
    #[ORM\JoinColumn(name: 'ac_n2_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?AcN2 $acN2 = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    public function __construct(AcN2 $acN2, string $name)
    {
        $this->acN2 = $acN2;
        $this->name = trim($name);

        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAcN2(): AcN2
    {
        return $this->acN2;
    }

    public function setAcN2(AcN2 $acN2): void
    {
        $this->acN2 = $acN2;
    }

    public function unsetAcN2(): void
    {
        $this->acN2 = null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}