<?php

namespace App\Entity;

use App\Repository\DeckRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

//Classe Deck, qui représente le deck que l'utilisateur cherche à construire
#[ORM\Entity(repositoryClass: DeckRepository::class)]
class Deck
{
    
    // Identifiant unique du deck, auto-généré par la base de données.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nom du deck, chaîne de caractères avec une longueur maximale de 20 caractères.
    #[ORM\Column(length: 20)]
    private ?string $deckName = null;

    // Date de création du deck, stockée en tant que DateTime.
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    // Date de dernière mise à jour du deck, peut être nulle.
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updateDate = null;

    // Statut public/privé du deck, représenté par un booléen.
    #[ORM\Column]
    private ?bool $status = null;

    // Description du deck, chaîne de caractères facultative (peut être nulle), longueur maximale de 150 caractères.
    #[ORM\Column(length: 150, nullable: true)]
    private ?string $deckDescription = null;

    // Statut de légalité du deck, booléen facultatif (peut être nul).
    #[ORM\Column(nullable: true)]
    private ?bool $isLegal = null;

    // Comptage des couleurs de mana dans le deck, stocké en format JSON.
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $colorCount = null;

    // Représentation du commandant du deck pour les formats nécessitant un commandant (comme le Commander)
    #[ORM\Column(nullable: true)]
    private ?array $commander = null;

    // Relation ManyToOne avec l'utilisateur propriétaire du deck, suppression en cascade
    // (Si l'utilisateur supprime son compte, ses decks sont également supprimés)
    #[ORM\ManyToOne(inversedBy: 'decks')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    // Relation ManyToOne avec l'entité Format, représentant le format de jeu associé au deck.
    #[ORM\ManyToOne(inversedBy: 'decks')]
    private ?Format $format = null;

    /**
     * @var Collection<int, Composition>
     * Relation OneToMany avec l'entité Composition, qui définit quelles cartes et en quelle quantité elles sont présentes dans une des trois listes associées au deck (Mainboard, Sideboard, Maybeboard) deck
     */
    #[ORM\OneToMany(targetEntity: Composition::class, mappedBy: 'deck')]
    private Collection $compositions;

    /**
     * @var Collection<int, User>
     * Relation ManyToMany avec l'entité User, représentant les utilisateurs qui ont ajouté le deck à leur favoris
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'decksLiked')]
    private Collection $likes;

    /**
     * @var Collection<int, Comment>
     * Relation OneToMany avec l'entité Comment, représentant les commentaires postés par les utilisateurs sur la page du deck
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'deck')]
    private Collection $comments;


    // Constructeur qui initialise les collections (compositions, likes, commentaires)
    public function __construct()
    {
        $this->compositions = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    // Getter pour l'identifiant du deck.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour le nom du deck.
    public function getDeckName(): ?string
    {
        return $this->deckName;
    }

    // Setter pour définir le nom du deck.
    public function setDeckName(string $deckName): static
    {
        $this->deckName = $deckName;
        return $this;
    }

    // Getter pour la date de création du deck.
    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    // Setter pour définir la date de création du deck.
    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    // Getter pour la date de dernière mise à jour du deck.
    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    // Setter pour définir la date de dernière mise à jour du deck.
    public function setUpdateDate(?\DateTimeInterface $updateDate): static
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    // Getter pour le statut public/privé du deck.
    public function isStatus(): ?bool
    {
        return $this->status;
    }

    // Setter pour définir le statut public/privé du deck.
    public function setStatus(bool $status): static
    {
        $this->status = $status;
        return $this;
    }

    // Getter pour la description du deck.
    public function getDeckDescription(): ?string
    {
        return $this->deckDescription;
    }

    // Setter pour définir la description du deck.
    public function setDeckDescription(?string $deckDescription): static
    {
        $this->deckDescription = $deckDescription;
        return $this;
    }

    // Getter pour récupérer le statut de légalité du deck.
    public function isLegal(): ?bool
    {
        return $this->isLegal;
    }

    // Setter pour définir le statut de légalité du deck.
    public function setLegal(?bool $isLegal): static
    {
        $this->isLegal = $isLegal;
        return $this;
    }
    
    // Getter pour récupérer le format du deck.
    public function getFormat(): ?Format
    {
        return $this->format;
    }

    // Setter pour définir le format du deck.
    public function setFormat(?Format $format): static
    {
        $this->format = $format;
        return $this;
    }

    // Getter pour récupérer l'utilisateur propriétaire du deck.
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Setter pour définir l'utilisateur propriétaire du deck.
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<int, Composition>
     * Getter pour récupérer la collection des compositions du deck (récupérer les trois listes de cartes associées à un deck)
     * Il s'agit de pouvoir récupérer quelles cartes sont présentes dans un deck, d'un user spécifique
     */
    public function getCompositions(): Collection
    {
        return $this->compositions;
    }

    /* Ajoute une composition à la collection des compositions du deck
       (Ajoute une association entre une carte, un deck, un user et une quantité)*/
    public function addComposition(Composition $composition): static
    {
        if (!$this->compositions->contains($composition)) {
            $this->compositions->add($composition);
            $composition->setDeck($this);
        }

        return $this;
    }

    // Retire une composition de la collection des compositions du deck
    //(utilisé quand on supprime une carte d'un deck)
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

    /**
     * @return Collection<int, User>
     * Getter pour récupérer la collection des likes du deck
     * (Permet de récupérer la liste des utilisateurs qui ont ajouté le deck en favoris)
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    // Ajoute un like à la collection des likes du deck
    //(fonction utilisée quand un utilisateur clique pour ajouter un deck à ses favoris)
    public function addLike(User $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }
        return $this;
    }

    // Retire un like de la collection des likes du deck
    //(fonction utilisée quand un utilisateur clique pour retirer un deck de ses favoris)
    public function removeLike(User $like): static
    {
        $this->likes->removeElement($like);
        return $this;
    }

    /**
     * @return Collection<int, Comment>
     * Getter pour récupérer la collection des commentaires postés par les utilisateurs sur la page du deck
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    // Fonction utilisée quand un utilisateur rédige et ajoute un commentaire depuis le formulaire correspondant sur la page deck
    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setDeck($this);
        }

        return $this;
    }

    // Fonction utilisée pour retirer un commentaire de la collection de commentaires du deck
    // (Quand un utilisateur supprime un de ses commentaires du deck)
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

    // Getter pour récupérer le commandant du deck (s'il y en a un).
    public function getCommander(): ?array
    {
        return $this->commander;
    }

    // Setter pour définir le commandant du deck.
    public function setCommander(?array $commander): static
    {
        $this->commander = $commander;

        return $this;
    }

    // Setter pour définir le tableau des couleurs du deck
    // (Quelles couleurs de mana et en quelles quantité sont-elles présentes dans le deck)
    public function setColorCount(?array $colorCount): static 
    {
        $this->colorCount = $colorCount;

        return $this;
    }

    // Getter pour récupérer les couleurs du deck.    
    public function getColorCount()
    {

        return $this->colorCount;
    }
    
}
