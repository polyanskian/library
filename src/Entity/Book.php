<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @ORM\Table(name="book", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="name_author", columns={"name", "author"})
 * })
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $author = '';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $cover = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $file = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $date_read = null;

    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     */
    private bool $is_download = false;

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'author' => $this->getAuthor(),
            'cover' => $this->getCover(),
            'file' => $this->getFile(),
            'dateRead' => $this->getDateRead(),
            'isDownload' => $this->getIsDownload(),
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());
        $metadata->addPropertyConstraint('author', new Assert\NotBlank());

        $metadata->addConstraint(new UniqueEntity([
            'fields' => ['name', 'author'],
            'errorPath' => 'name',
        ]));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getCover(): string
    {
        return $this->cover ?? '';
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;
        return $this;
    }

    public function getFile(): string
    {
        return $this->file ?? '';
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getDateRead(): ?DateTimeInterface
    {
        return $this->date_read;
    }

    public function setDateRead(?DateTimeInterface $date_read): self
    {
        $this->date_read = $date_read;
        return $this;
    }

    public function getIsDownload(): bool
    {
        return $this->is_download;
    }

    public function setIsDownload(bool $is_download): self
    {
        $this->is_download = $is_download;
        return $this;
    }
}
