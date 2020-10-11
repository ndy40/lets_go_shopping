<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\Operation\CloneShoppingListsAction;
use App\Extensions\Doctrine\Owner\OwnerAware;
use App\Repository\ShoppingListRepository;
use App\Responses\TransitionListsResponse;
use App\Traits\TimestampableTrait;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Operation\FetchStatusesOperation;
use App\Controller\Operation\PatchStateOperation;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"shopping_lists:read"}},
 *     denormalizationContext={"groups"={"shopping_lists:write"}},
 *     itemOperations={
 *          "get",
 *             "patch"={
 *               "denormalization_context"={"groups": "shopping_list:patch"}
 *          },
 *          "delete",
 *          "patch",
 *          "put",
 *          "clone"={
 *              "method"="GET",
 *              "description"="Create a new shopping list from Template",
 *              "path"="/shopping_lists/{id}/clone",
 *              "controller"=CloneShoppingListsAction::class,
 *              "input"=false
 *          },
 *          "transition"={
 *              "method"="PATCH",
 *              "path"="/shopping_lists/{id}/statuses",
 *              "controller"=PatchStateOperation::class,
 *              "denormalization_context"={"groups": "shopping_list:state"}
 *          },
 *          "transition_statuses"={
 *              "method"="GET",
 *              "path"="/shopping_lists/{id}/statuses",
 *              "controller"=FetchStatusesOperation::class,
 *              "input"=false,
 *              "output"=TransitionListsResponse::class,
 *              "formats"={"json"}
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass=ShoppingListRepository::class)
 * @OwnerAware(fieldName="owner_id")
 * @ORM\HasLifecycleCallbacks()
 */
class ShoppingList
{
    use TimestampableTrait;

    const STATUS_DRAFT = 'DRAFT';

    const STATUS_TEMPLATE = 'TEMPLATE';

    const STATUS_PUBLISHED = 'PUBLISHED';

    const STATUS_CLOSED = 'CLOSED';


    const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
        self::STATUS_TEMPLATE,
        self::STATUS_CLOSED
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"shopping_lists:read"})
     * @ApiFilter(SearchFilter::class)
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"shopping_lists:read", "shopping_lists:write", "shopping_list:status"})
     * @Assert\Choice(choices=ShoppingList::STATUSES, message="unsupported status - {{value}}. Suppprted status: {{choices}}")
     * @ApiFilter(SearchFilter::class)
     * @Assert\NotBlank
     */
    private ?string $status;

    /**
     * @ORM\Column(type="guid", nullable=true)
     * @Groups({"shopping_lists:read"})
     * @ApiFilter(SearchFilter::class)
     */
    private ?string $channelId;

    /**
     * @ORM\ManyToMany(targetEntity=ShoppingItem::class)
     * @Groups({"shopping_lists:read", "shopping_lists:write", "shopping_list:patch"})
     */
    private ?Collection $shoppingItems;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="shoppingLists")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $owner;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"shopping_lists:read", "shopping_lists:write", "shopping_list:patch"})
     * @ApiFilter(SearchFilter::class, strategy="partial")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    private ?string $title;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"shopping_lists:read"})
     */
    protected \DateTime $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Groups({"shopping_lists:read"})
     */
    protected \DateTime $updatedAt;


    public function __construct()
    {
        $this->shoppingItems = new ArrayCollection();
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

    /**
     * @ORM\PrePersist()
     */
    public function onStatusPrePersist()
    {
        if (is_null($this->status)) {
            $this->status = self::STATUS_DRAFT;
        }
    }

    /**
     * Sets createdAt.
     *
     * @param  ?\DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(?\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets updatedAt.
     *
     * @param  ?\DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(?\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
