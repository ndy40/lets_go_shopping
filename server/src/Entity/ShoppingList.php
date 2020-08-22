<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ShoppingListRepository;
use App\Traits\TimestampableTrait;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Extensions\Doctrine\Owner\OwnerAware;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"shopping_lists:read"}},
 *     denormalizationContext={"groups"={"shopping_lists:write"}}
 * )
 * @ORM\Entity(repositoryClass=ShoppingListRepository::class)
 * @OwnerAware(fieldName="owner_id")
 * @ORM\HasLifecycleCallbacks()
 */
class ShoppingList
{
    use TimestampableEntity, TimestampableTrait;

    const STATUSES = [
      'DRAFT',
      'TEMPLATE',
      'PUBLISHED',
      'CLOSED'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"shopping_lists:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"shopping_lists:read", "shopping_lists:write"})
     * @Assert\Choice(choices=ShoppingList::STATUSES, message="unsupported status - {{value}}. Suppprted status: {{choices}}")
     */
    private $status;

    /**
     * @ORM\Column(type="guid", nullable=true)
     * @Groups({"shopping_lists:read"})
     */
    private $channelId;

    /**
     * @ORM\ManyToMany(targetEntity=ShoppingItem::class)
     * @Groups({"shopping_lists:read", "shopping_lists:write"})
     */
    private $shoppingItems;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="shoppingLists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="sharedShoppingLists")
     * @Groups({"shopping_lists:read", "shopping_lists:write"})
     */
    private $collaborators;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"shopping_lists:read", "shopping_lists:write"})
     */
    private $title;


    public function __construct()
    {
        $this->shoppingItems = new ArrayCollection();
        $this->collaborators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getChannelId(): ?string
    {
        return $this->channelId;
    }

    public function setChannelId(?string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
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
        }

        return $this;
    }

    public function removeShoppingItem(ShoppingItem $shoppingItem): self
    {
        if ($this->shoppingItems->contains($shoppingItem)) {
            $this->shoppingItems->removeElement($shoppingItem);
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onChannelIdPersist()
    {
        $this->channelId = uuid_create(UUID_TYPE_TIME);
    }

    /**
     * @return Collection|User[]
     */
    public function getCollaborators(): Collection
    {
        return $this->collaborators;
    }

    public function addCollaborator(User $collaborator): self
    {
        if (!$this->collaborators->contains($collaborator)) {
            $this->collaborators[] = $collaborator;
        }

        return $this;
    }

    public function removeCollaborator(User $collaborator): self
    {
        if ($this->collaborators->contains($collaborator)) {
            $this->collaborators->removeElement($collaborator);
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onTitlePrePersist()
    {
        if (is_null($this->title)) {
            $this->title = 'Shopping List ' . (Carbon::now())->toRfc7231String();
        }
    }
}
