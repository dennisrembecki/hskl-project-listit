<?php

namespace App\Entity;

use App\Repository\ListeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ListeRepository::class)
 */
class Liste
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rating;

    /**
     * @ORM\OneToMany(targetEntity=ListeElement::class, mappedBy="liste")
     */
    private $elements;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="lists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $private;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sort;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="favorits")
     */
    private $users;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showdate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showinfo;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|ListeElement[]
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(ListeElement $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements[] = $element;
            $element->setListe($this);
        }

        return $this;
    }

    public function removeElement(ListeElement $element): self
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getListe() === $this) {
                $element->setListe(null);
            }
        }

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function setSort(?string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addFavorit($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeFavorit($this);
        }

        return $this;
    }

    public function getShowdate(): ?bool
    {
        return $this->showdate;
    }

    public function setShowdate(?bool $showdate): self
    {
        $this->showdate = $showdate;

        return $this;
    }

    public function getShowinfo(): ?bool
    {
        return $this->showinfo;
    }

    public function setShowinfo(?bool $showinfo): self
    {
        $this->showinfo = $showinfo;

        return $this;
    }
}
