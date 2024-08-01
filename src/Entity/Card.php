<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $scryfallId = null;

    /**
     * @var Collection<int, Deck>
     */
    #[ORM\ManyToMany(targetEntity: Deck::class, mappedBy: 'cards')]
    private Collection $decks;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $normalImageUrl = null;

    public function __construct()
    {
        $this->decks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScryfallId(): ?string
    {
        return $this->scryfallId;
    }

    public function setScryfallId(string $scryfallId): static
    {
        $this->scryfallId = $scryfallId;

        return $this;
    }

    /**
     * @return Collection<int, Deck>
     */
    public function getDecks(): Collection
    {
        return $this->decks;
    }

    public function addDeck(Deck $deck): static
    {
        if (!$this->decks->contains($deck)) {
            $this->decks->add($deck);
            $deck->addCard($this);
        }

        return $this;
    }

    public function removeDeck(Deck $deck): static
    {
        if ($this->decks->removeElement($deck)) {
            $deck->removeCard($this);
        }

        return $this;
    }

    public function getNormalImageUrl(): ?string
    {
        return $this->normalImageUrl;
    }

    public function setNormalImageUrl(?string $normalImageUrl): static
    {
        $this->normalImageUrl = $normalImageUrl;

        return $this;
    }
}
