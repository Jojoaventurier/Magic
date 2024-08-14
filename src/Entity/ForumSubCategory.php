<?php

namespace App\Entity;

use App\Repository\ForumSubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumSubCategoryRepository::class)]
class ForumSubCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $subCategoryName = null;

    #[ORM\ManyToOne(inversedBy: 'subCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ForumCategory $forumCategory = null;

    /**
     * @var Collection<int, ForumTopic>
     */
    #[ORM\OneToMany(targetEntity: ForumTopic::class, mappedBy: 'forumSubCategory')]
    private Collection $forumTopics;

    public function __construct()
    {
        $this->forumTopics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubCategoryName(): ?string
    {
        return $this->subCategoryName;
    }

    public function setSubCategoryName(string $subCategoryName): static
    {
        $this->subCategoryName = $subCategoryName;

        return $this;
    }

    public function getForumCategory(): ?ForumCategory
    {
        return $this->forumCategory;
    }

    public function setForumCategory(?ForumCategory $forumCategory): static
    {
        $this->forumCategory = $forumCategory;

        return $this;
    }

    /**
     * @return Collection<int, ForumTopic>
     */
    public function getForumTopics(): Collection
    {
        return $this->forumTopics;
    }

    public function addForumTopic(ForumTopic $forumTopic): static
    {
        if (!$this->forumTopics->contains($forumTopic)) {
            $this->forumTopics->add($forumTopic);
            $forumTopic->setForumSubCategory($this);
        }

        return $this;
    }

    public function removeForumTopic(ForumTopic $forumTopic): static
    {
        if ($this->forumTopics->removeElement($forumTopic)) {
            // set the owning side to null (unless already changed)
            if ($forumTopic->getForumSubCategory() === $this) {
                $forumTopic->setForumSubCategory(null);
            }
        }

        return $this;
    }

    public function __toString() 
    {
        return $this->subCategoryName;
    }
}
