<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\CheeseListingRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "get"={},
 *     "post"
 * },
 *     itemOperations={
 *     "get",
 *     "put"
 * },
 *
 *     normalizationContext={"groups"={"cheese_listing:read"}},
 *     denormalizationContext={"groups"={"cheese_listing:write"}},
 *     shortName="cheeses",
 *     attributes={
 *          "pagination_items_per_page"=3
 *     }
 *
 * )
 * @ORM\Entity(repositoryClass=CheeseListingRepository::class)
 * @ApiFilter(BooleanFilter::class, properties={"isPublished"})
 * @ApiFilter(SearchFilter::class, properties={"title": "partial", "description": "partial"})
 * @ApiFilter(RangeFilter::class, properties={"price"})
 * @ApiFilter(PropertyFilter::class)
 */
class CheeseListing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"cheese_listing:read"})
     * @Groups({"cheese_listing:write"})
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"cheese_listing:read"})
     */
    private ?string $description;

    /**
     * Price in cents
     * @ORM\Column(type="integer")
     * @Groups({"cheese_listing:read"})
     * @Groups({"cheese_listing:write"})
     */
    private ?int $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"cheese_listing:read"})
     */
    private ?bool $isPublished = false;

    public function __construct(string $title = null)
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->title = $title;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     *
     * @Groups({"cheese_listing:read"})
     *
     * @noinspection PhpPureAttributeCanBeAddedInspection
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 10) {
            return $this->description;
        }

        return substr($this->description, 0, 10) . '...';
    }

    /**
     * @Groups({"cheese_listing:write"})
     * @SerializedName("description")
     *
     * @param string $description
     * @return CheeseListing
     */
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @Groups({"cheese_listing:read"})
     * @return string
     */
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }
}
