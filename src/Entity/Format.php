<?php

namespace App\Entity;

use App\Repository\FormatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormatRepository::class)]
class Format
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $minNbOfCards = null;

    #[ORM\Column]
    private ?int $maxNbOfCards = null;

    /**
     * @var Collection<int, Deck>
     */
    #[ORM\OneToMany(targetEntity: Deck::class, mappedBy: 'format')]
    private Collection $decks;

    #[ORM\Column(length: 20)]
    private ?string $formatName = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxQuantity = null;

    public function __construct()
    {
        $this->decks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMinNbOfCards(): ?int
    {
        return $this->minNbOfCards;
    }

    public function setMinNbOfCards(int $minNbOfCards): static
    {
        $this->minNbOfCards = $minNbOfCards;

        return $this;
    }

    public function getMaxNbOfCards(): ?int
    {
        return $this->maxNbOfCards;
    }

    public function setMaxNbOfCards(int $maxNbOfCards): static
    {
        $this->maxNbOfCards = $maxNbOfCards;

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
            $deck->setFormat($this);
        }

        return $this;
    }

    public function removeDeck(Deck $deck): static
    {
        if ($this->decks->removeElement($deck)) {
            // set the owning side to null (unless already changed)
            if ($deck->getFormat() === $this) {
                $deck->setFormat(null);
            }
        }

        return $this;
    }

    public function getFormatName(): ?string
    {
        return $this->formatName;
    }

    public function setFormatName(string $formatName): static
    {
        $this->formatName = $formatName;

        return $this;
    }

    public function getMaxQuantity(): ?int
    {
        return $this->maxQuantity;
    }

    public function setMaxQuantity(int $maxQuantity): static
    {
        $this->maxQuantity = $maxQuantity;

        return $this;
    }
}
