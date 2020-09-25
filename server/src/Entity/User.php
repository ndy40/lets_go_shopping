<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Requests\SignUpRequests;
use App\Requests\ChangePasswordRequests;
use App\Controller\SendResetPasswordController;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 * @UniqueEntity(fields={"email"}, message="email already exists")
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *     normalizationContext={"groups"={"user:read"}},
 *     itemOperations={"get"},
 *     collectionOperations={
 *      "register"={
 *          "method"="post",
 *          "path"="/users/register",
 *          "input"=SignUpRequests::class
 *       },
 *       "reset_password"={
 *          "method": "POST",
 *          "path"="/users/reset_password",
 *          "controller"=SendResetPasswordController::class,
 *          "output"=false,
 *          "input"=SignUpRequests::class,
 *          "denormalization_context"={"groups"={"user:reset_password"}}
 *       },
 *       "change_password"={
 *          "method"="POST",
 *          "path"="/users/change_password",
 *          "input"=ChangePasswordRequests::class
 *       }
 *     }
 * )
 */
class User implements UserInterface
{
    use TimestampableEntity, TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     * @Groups({"user:register", "user:reset_password", "user:read"})
     * @Assert\NotNull(groups={"user:register", "user:reset_password"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user:register", "user:change_password"})
     * @Assert\NotNull(groups={"user:register"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:register", "user:read"})
     * @Assert\Type(type="string")
     * @Assert\NotNull(groups={"user:register"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type(type="string")
     * @Groups({"user:register", "user:read"})
     * @Assert\NotNull(groups={"user:register"})
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $resetToken;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $verifyToken;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     */
    private $isVerified;

    /**
     * @ORM\OneToMany(targetEntity=ShoppingItem::class, mappedBy="owner", orphanRemoval=true)
     */
    private $shoppingItems;

    /**
     * @ORM\OneToMany(targetEntity=ShoppingList::class, mappedBy="owner", orphanRemoval=true)
     */
    private $shoppingLists;

    public function __construct()
    {
        $this->shoppingItems = new ArrayCollection();
        $this->shoppingLists = new ArrayCollection();
        $this->sharedShoppingLists = new ArrayCollection();
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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getResetToken(): string
    {
        return $this->resetToken;
    }

    /**
     * @param ?string $resetToken
     */
    public function setResetToken(?string $resetToken): void
    {
        $this->resetToken = $resetToken;
    }

    /**
     * @return string
     */
    public function getVerifyToken(): ?string
    {
        return $this->verifyToken;
    }

    /**
     * @param ?string $verifyToken
     */
    public function setVerifyToken(?string $verifyToken): void
    {
        $this->verifyToken = $verifyToken;
    }

    /**
     * @return bool
     */
    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    /**
     * @param bool $isVerified
     */
    public function setIsVerified(?bool $isVerified): void
    {
        $this->isVerified = $isVerified;
    }

    /**
     * @return Collection|ShoppingItem[]
     */
    public function getShoppingItems(): Collection
    {
        return $this->shoppingItems;
    }

    public function addShoppingItem(ShoppingItem $shoppingItem): self
    {
        if (!$this->shoppingItems->contains($shoppingItem)) {
            $this->shoppingItems[] = $shoppingItem;
            $shoppingItem->setOwner($this);
        }

        return $this;
    }

    public function removeShoppingItem(ShoppingItem $shoppingItem): self
    {
        if ($this->shoppingItems->contains($shoppingItem)) {
            $this->shoppingItems->removeElement($shoppingItem);
            // set the owning side to null (unless already changed)
            if ($shoppingItem->getOwner() === $this) {
                $shoppingItem->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ShoppingList[]
     */
    public function getShoppingLists(): Collection
    {
        return $this->shoppingLists;
    }

    public function addShoppingList(ShoppingList $shoppingList): self
    {
        if (!$this->shoppingLists->contains($shoppingList)) {
            $this->shoppingLists[] = $shoppingList;
            $shoppingList->setOwner($this);
        }

        return $this;
    }

    public function removeShoppingList(ShoppingList $shoppingList): self
    {
        if ($this->shoppingLists->contains($shoppingList)) {
            $this->shoppingLists->removeElement($shoppingList);
            // set the owning side to null (unless already changed)
            if ($shoppingList->getOwner() === $this) {
                $shoppingList->setOwner(null);
            }
        }

        return $this;
    }
}
