<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

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
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $cover;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $file;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $date_read;

    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     */
    private bool $is_download;

    public function getFilePath(): string
    {
        $file = $this->getFile();
        return ($file) ? "{$this->getDirUpload()}/$file" : '';
    }

    public function getCoverPath(): string
    {
        $cover = $this->getCover();
        return ($cover) ? "{$this->getDirUpload()}/$cover" : '';
    }

    public function getDirUpload(): string
    {
        return "book/{$this->getId()}";
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new NotBlank());

        $metadata->addPropertyConstraint('date_read', new NotBlank());
        $metadata->addPropertyConstraint('date_read', new Type(\DateTime::class));

        $metadata->addPropertyConstraint('author', new NotBlank());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = (string) $name;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = (string) $author;
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

    public function getDateRead(): \DateTimeInterface
    {
        if (!isset($this->date_read)) {
            $this->date_read = new \DateTime();
        }

        return $this->date_read;
    }

    public function setDateRead(\DateTimeInterface $date_read): self
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
