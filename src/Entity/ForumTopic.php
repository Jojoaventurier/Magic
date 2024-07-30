<?php

namespace App\Entity;

use App\Repository\ForumTopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumTopicRepository::class)]
class ForumTopic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $topicTitle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $editDate = null;

    #[ORM\ManyToOne(inversedBy: 'forumTopics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ForumSubCategory $forumSubCategory = null;

    /**
     * @var Collection<int, ForumPost>
     */
    #[ORM\OneToMany(targetEntity: ForumPost::class, mappedBy: 'forumTopic')]
    private Collection $forumPosts;

    #[ORM\Column(nullable: true)]
    private ?bool $closed = null;

    #[ORM\ManyToOne(inversedBy: 'forumTopics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->forumPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopicTitle(): ?string
    {
        return $this->topicTitle;
    }

    public function setTopicTitle(string $topicTitle): static
    {
        $this->topicTitle = $topicTitle;

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

    public function getEditDate(): ?\DateTimeInterface
    {
        return $this->editDate;
    }

    public function setEditDate(\DateTimeInterface $editDate): static
    {
        $this->editDate = $editDate;

        return $this;
    }

    public function getForumSubCategory(): ?ForumSubCategory
    {
        return $this->forumSubCategory;
    }

    public function setForumSubCategory(?ForumSubCategory $forumSubCategory): static
    {
        $this->forumSubCategory = $forumSubCategory;

        return $this;
    }

    /**
     * @return Collection<int, ForumPost>
     */
    public function getForumPosts(): Collection
    {
        return $this->forumPosts;
    }

    public function addForumPost(ForumPost $forumPost): static
    {
        if (!$this->forumPosts->contains($forumPost)) {
            $this->forumPosts->add($forumPost);
            $forumPost->setForumTopic($this);
        }

        return $this;
    }

    public function removeForumPost(ForumPost $forumPost): static
    {
        if ($this->forumPosts->removeElement($forumPost)) {
            // set the owning side to null (unless already changed)
            if ($forumPost->getForumTopic() === $this) {
                $forumPost->setForumTopic(null);
            }
        }

        return $this;
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(?bool $closed): static
    {
        $this->closed = $closed;

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
}
