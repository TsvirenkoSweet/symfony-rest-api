<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "put"={
 *             "access_control"="is_granted('ROLE_EDITOR') or is_granted('ROLE_WRITER') and object.getAuthor() == user"
 *         }
 *     },
 *     collectionOperations={
 *          "get",
 *          "post"={
 *             "access_control"="is_granted('ROLE_WRITER')"
 *         }
 *     }
 * )
 */
class Product implements AuthoredEntityInterface, PublishDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=10)
     */
    private $product_title;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Assert\Type("float")
     */
    private $product_price;

    /**
     * @ORM\Column(type="string", length=5)
     * @Assert\NotBlank()
     */
    private $product_size;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $product_color;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10)
     */
    private $product_description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="posts")
     */
    private $comments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $published;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductTitle(): ?string
    {
        return $this->product_title;
    }

    public function setProductTitle(string $product_title): self
    {
        $this->product_title = $product_title;

        return $this;
    }

    public function getProductPrice(): ?float
    {
        return $this->product_price;
    }

    public function setProductPrice(?float $product_price): self
    {
        $this->product_price = $product_price;

        return $this;
    }

    public function getProductSize(): ?string
    {
        return $this->product_size;
    }

    public function setProductSize(string $product_size): self
    {
        $this->product_size = $product_size;

        return $this;
    }

    public function getProductColor(): ?string
    {
        return $this->product_color;
    }

    public function setProductColor(string $product_color): self
    {
        $this->product_color = $product_color;

        return $this;
    }

    public function getProductDescription(): ?string
    {
        return $this->product_description;
    }

    public function setProductDescription(string $product_description): self
    {
        $this->product_description = $product_description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     */
    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublishDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }
}
