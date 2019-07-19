<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
/**
 * @ApiResource(
 *     collectionOperations={
            "get","post"
 *     },
 *     itemOperations={
            "get"={
 *          "normalization_context"={"groups"={"cheese_listing:read","cheese_listing:item:get"}}
 *     },
 *          "put"
 *     },
 *     normalizationContext={"groups"={"cheese_listing:read"},"swagger_definition_name"="Read"},
 *     denormalizationContext={"groups"={"cheese_listing:write"},"swagger_definition_name"="Write"},
 *     shortName="cheeses",
 *     attributes={
            "pagination_items_per_page"=10,
 *          "formats"={"jsonld","json","html","jsonhal","csv"={"text/csv"}}
 *
 *     })
 *
 * @ORM\Entity(repositoryClass="App\Repository\CheeseListingRepository")
 * @ApiFilter(BooleanFilter::class, properties={"isPublished"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "title": "partial",
 *     "decription": "partial",
 *     "owner":  "exact",
 *     "owner.username": "partial"
 *     })
 * @ApiFilter(RangeFilter::class, properties={"price"})
 * @ApiFilter(PropertyFilter::class)
 *
 */
class CheeseListing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"cheese_listing:read","cheese_listing:write","user:read","user:write"})
     * @NotBlank()
     * @Length(
     *     min=2,
     *     max=50,
     *     maxMessage="Describe your cheese in 50 chars or less"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"cheese_listing:read"}  )
     * @NotBlank()
     */
    private $decription;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"cheese_listing:read","cheese_listing:write","user:read","user:write"} )
     * @NotBlank()
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="cheeseListings")
     * @Groups({"cheese_listing:read","cheese_listing:write"} )
     * @Assert\Valid( )
     */
    private $owner;

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

    public function getDecription(): ?string
    {
        return $this->decription;
    }

    public function setDescription(string $decription): self
    {
        $this->decription = $decription;


        return $this;
    }


    /**
     * The description of the cheese as raw text.
     * @SerializedName("description")
     * @Groups({"cheese_listing:write","user:write"})
     */
    public function setTextDescription(string $decription): self
    {
        $this->decription = nl2br($decription);

        return $this;
    }

    /**
     * @Groups("cheese_listing:read")
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->decription) < 40) {
            return $this->decription;
        }

        return substr($this->decription,0,40).'...';
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
     * How long ago in text that this cheese listing was added.
     *
     * @Groups("cheese_listing:read")
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }


}
