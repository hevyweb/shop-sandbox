<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @UniqueEntity(fields="name", message="Category with such name already exists.")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="children")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $created_by;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $updated_by;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Category
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param Category $parent
     * @return Category
     */
    public function setParent(Category $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param Category $child
     * @return Category
     */
    public function addChild(Category $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    /**
     * @param Category $child
     * @return Category
     */
    public function removeChild(Category $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @param \DateTimeInterface $created
     * @return Category
     */
    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCreatedBy(): ?self
    {
        return $this->created_by;
    }

    /**
     * @param User|null $created_by
     * @return Category
     */
    public function setCreatedBy(?User $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * @param \DateTimeInterface $updated
     * @return Category
     */
    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getUpdateBy(): ?self
    {
        return $this->updated_by;
    }

    /**
     * @param User|null $updated_by
     * @return Category
     */
    public function setUpdatedBy(?User $updated_by): self
    {
        $this->updated_by = $updated_by;

        return $this;
    }
}
