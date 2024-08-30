<?php

namespace App\Entity;

use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StateRepository::class)]
class State
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stateName = null;

    /**
     * @var Collection<int, Composition>
     */
    #[ORM\OneToMany(targetEntity: Composition::class, mappedBy: 'state')]
    private Collection $composition;

    public function __construct()
    {
        $this->composition = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStateName(): ?string
    {
        return $this->stateName;
    }

    public function setStateName(?string $stateName): static
    {
        $this->stateName = $stateName;

        return $this;
    }

    /**
     * @return Collection<int, Composition>
     */
    public function getComposition(): Collection
    {
        return $this->composition;
    }

    public function addComposition(Composition $composition): static
    {
        if (!$this->composition->contains($composition)) {
            $this->composition->add($composition);
            $composition->setState($this);
        }

        return $this;
    }

    public function removeComposition(Composition $composition): static
    {
        if ($this->composition->removeElement($composition)) {
            // set the owning side to null (unless already changed)
            if ($composition->getState() === $this) {
                $composition->setState(null);
            }
        }

        return $this;
    }
}
