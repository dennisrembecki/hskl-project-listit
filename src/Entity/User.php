<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="Es gibt bereits einen Nutzer mit dieser E-Mail.")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Liste::class, mappedBy="creator")
     */
    private $lists;

    /**
     * @ORM\OneToMany(targetEntity=ListeElement::class, mappedBy="creator")
     */
    private $elements;

    /**
     * @ORM\OneToMany(targetEntity=Vote::class, mappedBy="creator")
     */

    private $votes;

    /**
     * @ORM\ManyToMany(targetEntity=Liste::class, inversedBy="users")
     */
    private $favorits;

    public function __construct()
    {
        $this->lists = new ArrayCollection();
        $this->elements = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->favorits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection|Liste[]
     */
    public function getLists(): Collection
    {
        return $this->lists;
    }

    public function addList(Liste $list): self
    {
        if (!$this->lists->contains($list)) {
            $this->lists[] = $list;
            $list->setCreator($this);
        }

        return $this;
    }

    public function removeList(Liste $list): self
    {
        if ($this->lists->removeElement($list)) {
            // set the owning side to null (unless already changed)
            if ($list->getCreator() === $this) {
                $list->setCreator(null);
            }
        }

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
            $element->setCreator($this);
        }

        return $this;
    }

    public function removeElement(ListeElement $element): self
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getCreator() === $this) {
                $element->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setCreator($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getCreator() === $this) {
                $vote->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Liste[]
     */
    public function getFavorits(): Collection
    {
        return $this->favorits;
    }

    public function addFavorit(Liste $favorit): self
    {
        if (!$this->favorits->contains($favorit)) {
            $this->favorits[] = $favorit;
        }

        return $this;
    }

    public function removeFavorit(Liste $favorit): self
    {
        $this->favorits->removeElement($favorit);

        return $this;
    }
}
