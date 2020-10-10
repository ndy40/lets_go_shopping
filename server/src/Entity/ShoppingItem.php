<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Extensions\Doctrine\Owner\OwnerAware;
use App\Repository\ShoppingItemRepository;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 *     denormalizationContext={"groups"={"shopping_item:write"}},
 *     normalizationContext={"groups"={"shopping_item:read"}}
 * )
 * @ORM\Entity(repositoryClass=ShoppingItemRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @OwnerAware(fieldName="owner_id")
 */
class ShoppingItem
{
    use TimestampableEntity, TimestampableTrait;

    const STATUS = ['NOT_PICKED', "PICKED"];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"shopping_item:read"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @ApiFilter(SearchFilter::class, strategy="partial")
     * @Groups({"shopping_item:read", "shopping_item:write"})
     */
    private ?string $name;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 1})
     * @Assert\Positive()
     * @Groups({"shopping_item:read", "shopping_item:write"})
     */
    private ?int $quantity;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, options={"default": "NOT_PICKED"})
     * @Assert\Choice(choices=ShoppingItem::STATUS, message="Pick a valid status")
     * @ApiFilter(SearchFilter::class)
     * @Groups({"shopping_item:read", "shopping_item:write"})
     * @Assert\NotBlank
     */
    private ?string $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="shoppingItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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
    public function onPrePersistStatus()
    {
        if (is_null($this->status)) {
            $this->status = 'NOT_PICKED';
        }
    }
}
