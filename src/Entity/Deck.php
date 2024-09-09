<?php

namespace App\Entity;

use App\Repository\DeckRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeckRepository::class)]
class Deck
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $deckName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updateDate = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $deckDescription = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isLegal = null;

    /**
     * @var Collection<int, Composition>
     */
    #[ORM\OneToMany(targetEntity: Composition::class, mappedBy: 'deck')]
    private Collection $compositions;

    #[ORM\ManyToOne(inversedBy: 'decks')]
    private ?Format $format = null;

    #[ORM\ManyToOne(inversedBy: 'decks')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'decksLiked')]
    private Collection $likes;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'deck')]
    private Collection $comments;

    #[ORM\Column(nullable: true)]
    private ?array $commander = null;

    public function __construct()
    {
        $this->compositions = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeckName(): ?string
    {
        return $this->deckName;
    }

    public function setDeckName(string $deckName): static
    {
        $this->deckName = $deckName;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?\DateTimeInterface $updateDate): static
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDeckDescription(): ?string
    {
        return $this->deckDescription;
    }

    public function setDeckDescription(?string $deckDescription): static
    {
        $this->deckDescription = $deckDescription;

        return $this;
    }

    public function isLegal(): ?bool
    {
        return $this->isLegal;
    }

    public function setLegal(?bool $isLegal): static
    {
        $this->isLegal = $isLegal;

        return $this;
    }


    /**
     * @return Collection<int, Composition>
     */
    public function getCompositions(): Collection
    {
        return $this->compositions;
    }

    public function addComposition(Composition $composition): static
    {
        if (!$this->compositions->contains($composition)) {
            $this->compositions->add($composition);
            $composition->setDeck($this);
        }

        return $this;
    }

    public function removeComposition(Composition $composition): static
    {
        if ($this->compositions->removeElement($composition)) {
            // set the owning side to null (unless already changed)
            if ($composition->getDeck() === $this) {
                $composition->setDeck(null);
            }
        }

        return $this;
    }

    public function getFormat(): ?Format
    {
        return $this->format;
    }

    public function setFormat(?Format $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }

        return $this;
    }

    public function removeLike(User $like): static
    {
        $this->likes->removeElement($like);

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setDeck($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getDeck() === $this) {
                $comment->setDeck(null);
            }
        }

        return $this;
    }

    public function getCommander(): ?array
    {
        return $this->commander;
    }

    public function setCommander(?array $commander): static
    {
        $this->commander = $commander;

        return $this;
    }
}
