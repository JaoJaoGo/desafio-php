<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'ac_n2s')]
#[ORM\Index(name: 'idx_ac_n2_ac_id', columns: ['ac_id'])]
#[ORM\HasLifecycleCallbacks]
class AcN2
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 150)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Ac::class, inversedBy: 'acN2s')]
    #[ORM\JoinColumn(name: 'ac_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Ac $ac = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, Ar> */
    #[ORM\OneToMany(mappedBy: 'acN2', targetEntity: Ar::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $ars;

    public function __construct(Ac $ac, string $name)
    {
        $this->ac = $ac;
        $this->name = trim($name);

        $this->ars = new ArrayCollection();
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

    public function getAc(): Ac
    {
        // JoinColumn é nullable=false
        return $this->ac;
    }

    public function setAc(Ac $ac): void
    {
        $this->ac = $ac;
    }

    public function unsetAc(): void
    {
        $this->ac = null;
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

    /** @return Collection<int, Ar> */
    public function getArs(): Collection
    {
        return $this->ars;
    }

    public function addAr(Ar $ar): void
    {
        if (!$this->ars->contains($ar)) {
            $this->ars->add($ar);
            $ar->setAcN2($this);
        }
    }

    public function removeAr(Ar $ar): void
    {
        if ($this->ars->removeElement($ar)) {
            $ar->unsetAcN2();
        }
    }
}