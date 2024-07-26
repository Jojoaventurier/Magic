<?php

namespace App\Entity;

use App\Repository\ForumCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumCategoryRepository::class)]
class ForumCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $forumCategoryName = null;

    /**
     * @var Collection<int, ForumSubCategory>
     */
    #[ORM\OneToMany(targetEntity: ForumSubCategory::class, mappedBy: 'forumCategory')]
    private Collection $subCategories;

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForumCategoryName(): ?string
    {
        return $this->forumCategoryName;
    }

    public function setForumCategoryName(string $forumCategoryName): static
    {
        $this->forumCategoryName = $forumCategoryName;

        return $this;
    }

    /**
     * @return Collection<int, ForumSubCategory>
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(ForumSubCategory $subCategory): static
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories->add($subCategory);
            $subCategory->setForumCategory($this);
        }

        return $this;
    }

    public function removeSubCategory(ForumSubCategory $subCategory): static
    {
        if ($this->subCategories->removeElement($subCategory)) {
            // set the owning side to null (unless already changed)
            if ($subCategory->getForumCategory() === $this) {
                $subCategory->setForumCategory(null);
            }
        }

        return $this;
    }
}
