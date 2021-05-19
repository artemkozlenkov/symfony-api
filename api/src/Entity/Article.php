<?php

namespace Webapp\Entity;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_ADMIN')"},
 *     collectionOperations={
 *     "post",
 *      "get"
 * },
 *     itemOperations={
 *     "get",
 *      "put",
 *     "delete"
 * },
 *     normalizationContext={"groups"={"article:read"}},
 *     denormalizationContext={"groups"={"article:write"}}
 * )
 * @ORM\Entity
 */
class Article
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     *
     * @Groups({"article:read"})
     *
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"article:read", "article:write"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"article:read", "article:write"})
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"article:read", "article:write"})
     */
    private $content;

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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
