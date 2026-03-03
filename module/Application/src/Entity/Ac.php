<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'acs')]
#[ORM\HasLifecycleCallbacks]
class Ac
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 150)]
    private string $name;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, AcN2> */
    #[ORM\OneToMany(mappedBy: 'ac', targetEntity: AcN2::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $acN2s;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->acN2s = new ArrayCollection();
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

    /** @return Collection<int, AcN2> */
    public function getAcN2s(): Collection
    {
        return $this->acN2s;
    }

    public function addAcN2(AcN2 $acN2): void
    {
        if (!$this->acN2s->contains($acN2)) {
            $this->acN2s->add($acN2);
            $acN2->setAc($this);
        }
    }

    public function removeAcN2(AcN2 $acN2): void
    {
        if ($this->acN2s->removeElement($acN2)) {
            $acN2->unsetAc();
        }
    }
}